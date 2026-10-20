
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, addDoc, getDocs, updateDoc, deleteDoc, doc, onSnapshot, query } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // Firebase Configuration & Initialization
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'usp-equipment-booking';
        let firebaseConfig = {};
        try {
            firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {
                apiKey: "dummy", authDomain: "dummy.firebaseapp.com", projectId: "dummy"
            };
        } catch(e) { console.warn("Using dummy config for layout viewing"); }

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
        const auth = getAuth(app);

        // App State
        let currentUser = null; // { uid, username, role }
        let equipmentData = [];
        let bookingsData = [];
        let unsubEquipment = null;
        let unsubBookings = null;

        // DOM Elements
        const themeSelector = document.getElementById('theme-selector');
        const loginForm = document.getElementById('login-form');
        const viewLogin = document.getElementById('view-login');
        const viewStudent = document.getElementById('view-student');
        const viewAdmin = document.getElementById('view-admin');
        const navUserInfo = document.getElementById('nav-user-info');
        const navUsername = document.getElementById('nav-username');
        const btnLogout = document.getElementById('btn-logout');
        
        themeSelector.addEventListener('change', (e) => {
            document.body.className = `min-h-screen flex flex-col ${e.target.value}`;
        });

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const targetId = e.target.getAttribute('data-target');
                // Remove active from siblings
                Array.from(e.target.parentElement.children).forEach(sibling => sibling.classList.remove('active', 'border-usp-teal', 'text-usp-teal'));
                e.target.classList.add('active', 'border-usp-teal', 'text-usp-teal');
                
                // Hide all contents in this view
                const view = e.target.closest('.portal-card');
                view.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
                document.getElementById(targetId).classList.remove('hidden');
            });
        });

        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined') {
                    await signInWithCustomToken(auth, __initial_auth_token);
                } else {
                    await signInAnonymously(auth);
                }
            } catch (err) { console.error("Auth error:", err); }
        };

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const role = document.getElementById('login-role').value;
            const username = document.getElementById('login-username').value.trim();
            const usernameError = document.getElementById('username-error');

            if (role === 'student' && !/^S\d+$/i.test(username)) {
                usernameError.classList.remove('hidden');
                return;
            }
            usernameError.classList.add('hidden');
            
            document.getElementById('login-btn-text').textContent = 'Authenticating...';
            document.getElementById('login-loader').classList.remove('hidden');

            await initAuth();
            
            if(auth.currentUser) {
                currentUser = { uid: auth.currentUser.uid, username: username.toUpperCase(), role: role };
                showView(role);
                loadData();
            }
            
            document.getElementById('login-btn-text').textContent = 'Log In';
            document.getElementById('login-loader').classList.add('hidden');
        });

        btnLogout.addEventListener('click', () => {
            if(unsubEquipment) unsubEquipment();
            if(unsubBookings) unsubBookings();
            currentUser = null;
            showView('login');
        });

        const showView = (role) => {
            viewLogin.classList.add('hidden');
            viewStudent.classList.add('hidden');
            viewAdmin.classList.add('hidden');
            document.getElementById('chatbot-container').classList.add('hidden');

            if (role === 'login') {
                viewLogin.classList.remove('hidden');
                navUserInfo.classList.add('hidden');
            } else if (role === 'student') {
                viewStudent.classList.remove('hidden');
                document.getElementById('chatbot-container').classList.remove('hidden');
                setupNav();
            } else if (role === 'admin') {
                viewAdmin.classList.remove('hidden');
                setupNav();
            }
        };

        const setupNav = () => {
            navUserInfo.classList.remove('hidden');
            navUserInfo.classList.add('flex');
            navUsername.textContent = `${currentUser.role === 'admin' ? 'Admin' : 'Student'} (${currentUser.username})`;
        };

        const loadData = () => {
            if(!auth.currentUser) return;

            const eqRef = collection(db, 'artifacts', appId, 'public', 'data', 'equipment');
            const bkRef = collection(db, 'artifacts', appId, 'public', 'data', 'bookings');

            unsubEquipment = onSnapshot(query(eqRef), (snap) => {
                equipmentData = snap.docs.map(d => ({ id: d.id, ...d.data() }));
                renderEquipment();
                renderAdminInventory();
            }, (err) => console.error(err));

            unsubBookings = onSnapshot(query(bkRef), (snap) => {
                bookingsData = snap.docs.map(d => ({ id: d.id, ...d.data() }));
                renderBookings();
                renderAdminBookings();
            }, (err) => console.error(err));
        };

        // Initial Seed (if empty) - Admin only functionality technically, but good for demo
        const seedDataIfEmpty = async () => {
             // simplified for brevity in this artifact.
        }

        const renderEquipment = () => {
            const grid = document.getElementById('equipment-grid');
            const filter = document.getElementById('filter-category').value;
            const search = document.getElementById('search-equipment').value.toLowerCase();
            
            grid.innerHTML = '';
            
            equipmentData.filter(eq => {
                const matchCat = filter === 'all' || eq.category === filter;
                const matchSearch = eq.name.toLowerCase().includes(search);
                return matchCat && matchSearch;
            }).forEach(eq => {
                const available = parseInt(eq.qty) > 0;
                grid.innerHTML += `
                    <div class="bg-white border rounded-lg p-4 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-800">${eq.name}</h3>
                                <span class="text-xs px-2 py-1 rounded ${available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${available ? 'Available ('+eq.qty+')' : 'Out of Stock'}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">Category: ${eq.category}</p>
                        </div>
                        <button onclick="requestBooking('${eq.id}', '${eq.name}')" ${!available ? 'disabled' : ''} class="w-full py-2 rounded text-sm font-medium transition ${available ? 'bg-usp-dark text-white hover:bg-opacity-90' : 'bg-gray-300 text-gray-500 cursor-not-allowed'}">
                            ${available ? 'Request Booking' : 'Unavailable'}
                        </button>
                    </div>
                `;
            });
        };

        document.getElementById('search-equipment').addEventListener('input', renderEquipment);
        document.getElementById('filter-category').addEventListener('change', renderEquipment);

        window.requestBooking = async (eqId, eqName) => {
            if(!auth.currentUser || currentUser.role !== 'student') return;
            try {
                await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'bookings'), {
                    equipmentId: eqId,
                    equipmentName: eqName,
                    studentId: currentUser.uid,
                    studentUsername: currentUser.username,
                    status: 'Pending',
                    requestDate: new Date().toISOString(),
                    returnDate: null
                });
                // Optimistic UI feedback could go here
            } catch (e) { console.error("Error booking", e); }
        };

        const renderBookings = () => {
            if(currentUser?.role !== 'student') return;
            const tbody = document.getElementById('student-bookings-table');
            tbody.innerHTML = '';
            
            const myBookings = bookingsData.filter(b => b.studentId === currentUser.uid);
            
            if(myBookings.length === 0){
                tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No bookings found.</td></tr>`;
                return;
            }

            myBookings.sort((a,b) => new Date(b.requestDate) - new Date(a.requestDate)).forEach(b => {
                let statusColor = b.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                  b.status === 'Approved' ? 'bg-green-100 text-green-800' :
                                  b.status === 'Returned' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800';
                
                tbody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${b.equipmentName}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(b.requestDate).toLocaleDateString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">${b.status}</span>
                        </td>
                    </tr>
                `;
            });
        };

        const renderAdminInventory = () => {
            const grid = document.getElementById('admin-inventory-grid');
            grid.innerHTML = '';
            equipmentData.forEach(eq => {
                grid.innerHTML += `
                    <div class="bg-white border rounded p-3 text-sm">
                        <div class="font-bold">${eq.name}</div>
                        <div class="text-gray-500 mb-2">Category: ${eq.category} | Qty: ${eq.qty}</div>
                        <button onclick="deleteEquipment('${eq.id}')" class="text-red-600 hover:underline text-xs">Delete</button>
                    </div>
                `;
            });
        };

        document.getElementById('btn-add-item').addEventListener('click', () => {
            document.getElementById('add-equipment-form').classList.remove('hidden');
        });
        document.getElementById('btn-cancel-add').addEventListener('click', () => {
            document.getElementById('add-equipment-form').classList.add('hidden');
        });

        document.getElementById('add-equipment-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            if(!auth.currentUser || currentUser.role !== 'admin') return;
            
            const name = document.getElementById('inv-name').value;
            const cat = document.getElementById('inv-category').value;
            const qty = parseInt(document.getElementById('inv-qty').value);
            
            try {
                await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'equipment'), {
                    name: name, category: cat, qty: qty
                });
                document.getElementById('add-equipment-form').reset();
                document.getElementById('add-equipment-form').classList.add('hidden');
            } catch (err) { console.error(err); }
        });

        window.deleteEquipment = async (id) => {
            if(!auth.currentUser || currentUser.role !== 'admin') return;
            try { await deleteDoc(doc(db, 'artifacts', appId, 'public', 'data', 'equipment', id)); } 
            catch(err) { console.error(err); }
        };

        const renderAdminBookings = () => {
            const tbody = document.getElementById('admin-bookings-table');
            tbody.innerHTML = '';
            
            bookingsData.sort((a,b) => new Date(b.requestDate) - new Date(a.requestDate)).forEach(b => {
                let actionButtons = '';
                if(b.status === 'Pending') {
                    actionButtons = `
                        <button onclick="updateBooking('${b.id}', 'Approved', '${b.equipmentId}')" class="text-green-600 hover:underline mr-2 text-xs font-bold">Approve</button>
                        <button onclick="updateBooking('${b.id}', 'Rejected', null)" class="text-red-600 hover:underline text-xs font-bold">Reject</button>
                    `;
                } else if(b.status === 'Approved') {
                    actionButtons = `<button onclick="updateBooking('${b.id}', 'Returned', '${b.equipmentId}')" class="text-blue-600 hover:underline text-xs font-bold">Mark Returned</button>`;
                }

                tbody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${b.studentUsername}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${b.equipmentName}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${b.status}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">${actionButtons}</td>
                    </tr>
                `;
            });
        };

        window.updateBooking = async (bookingId, newStatus, eqId) => {
            if(!auth.currentUser || currentUser.role !== 'admin') return;
            try {
                await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'bookings', bookingId), {
                    status: newStatus,
                    returnDate: newStatus === 'Returned' ? newStatus : null
                });

                // Adjust inventory quantity
                if((newStatus === 'Approved' || newStatus === 'Returned') && eqId) {
                    const eqRef = doc(db, 'artifacts', appId, 'public', 'data', 'equipment', eqId);
                    const eq = equipmentData.find(e => e.id === eqId);
                    if(eq) {
                        const modifier = newStatus === 'Approved' ? -1 : 1;
                        await updateDoc(eqRef, { qty: Math.max(0, parseInt(eq.qty) + modifier) });
                    }
                }
            } catch(err) { console.error(err); }
        };

        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatbotClose = document.getElementById('chatbot-close');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const chatMessages = document.getElementById('chat-messages');

        chatbotToggle.addEventListener('click', () => chatbotWindow.classList.toggle('hidden'));
        chatbotClose.addEventListener('click', () => chatbotWindow.classList.add('hidden'));

        const appendMessage = (text, isUser = false) => {
            const msgDiv = document.createElement('div');
            msgDiv.className = `p-2 rounded-lg max-w-[85%] text-sm ${isUser ? 'bg-usp-teal text-white self-end ml-auto' : 'bg-gray-200 text-gray-800 self-start'}`;
            msgDiv.textContent = text;
            chatMessages.appendChild(msgDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };

        const handleChat = async () => {
            const queryText = chatInput.value.trim();
            if(!queryText) return;
            
            appendMessage(queryText, true);
            chatInput.value = '';
            
            const eqContext = equipmentData.map(e => `${e.name} (${e.qty} available)`).join(', ');
            const systemPrompt = `You are a helpful AI assistant for the University of the South Pacific (USP) Equipment Booking System. 
                Answer student queries accurately. 
                Current Equipment Available: ${eqContext || 'None currently listed.'}. 
                Keep responses concise, friendly, and related strictly to university equipment booking.`;

            const payload = {
                contents: [{ parts: [{ text: queryText }] }],
                systemInstruction: { parts: [{ text: systemPrompt }] },
            };

            const apiKey = ""; // Canvas handles API key injection
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=${apiKey}`;

            try {
                appendMessage("Thinking...", false); // Loading indicator
                const loadingNode = chatMessages.lastChild;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                chatMessages.removeChild(loadingNode);
                
                if (result.candidates && result.candidates[0].content.parts[0].text) {
                    appendMessage(result.candidates[0].content.parts[0].text, false);
                } else {
                    appendMessage("I'm sorry, I encountered an error processing your request.", false);
                }
            } catch (err) {
                console.error("Gemini Error:", err);
                chatMessages.removeChild(chatMessages.lastChild);
                appendMessage("Network error trying to reach AI. Please try again later.", false);
            }
        };

        chatSend.addEventListener('click', handleChat);
        chatInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') handleChat();
        });
