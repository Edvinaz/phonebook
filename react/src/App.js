import React, { useState } from 'react';

const App = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [message, setMessage] = useState('');
    const [token, setToken] = useState(null);
    const [phonebookList, setPhonebookList] = useState([]);
    const [newContact, setNewContact] = useState({ name: '', phone: '' });
    const [shareEmail, setShareEmail] = useState('');
    const [editingContact, setEditingContact] = useState(null);
    const [updatedContact, setUpdatedContact] = useState({ name: '', phone: '' });

    const apiUrl = 'http://localhost:8088/api';

    const handleLogin = async (e) => {
        e.preventDefault();
        const loginData = { username, password };

        try {
            const response = await fetch(`${apiUrl}/login_check`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(loginData),
            });

            if (response.ok) {
                const data = await response.json();
                setMessage(`Welcome, ${username}!`);
                setToken(data.token);
                fetchPhonebookList(data.token);
            } else {
                setMessage('Invalid username or password.');
            }
        } catch (error) {
            setMessage('An error occurred during login.');
            console.error('Login error:', error);
        }
    };

    const fetchPhonebookList = async (token) => {
        try {
            const response = await fetch(`${apiUrl}/phonebook`, {
                method: 'GET',
                headers: { Authorization: `Bearer ${token}` },
            });

            if (response.ok) {
                const data = await response.json();
                setPhonebookList(data);
            } else {
                setMessage('Failed to fetch phonebook data.');
            }
        } catch (error) {
            setMessage('An error occurred while fetching the phonebook.');
            console.error('Fetch phonebook error:', error);
        }
    };

    const handleAddContact = async () => {
        try {
            const response = await fetch(`${apiUrl}/add-contact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(newContact),
            });

            if (response.ok) {
                setMessage('Contact added successfully.');
                fetchPhonebookList(token); // Refresh the list
                setNewContact({ name: '', phone: '' }); // Clear the form
            } else {
                setMessage('Failed to add contact.');
            }
        } catch (error) {
            setMessage('An error occurred while adding the contact.');
            console.error('Add contact error:', error);
        }
    };

    const handleUpdateContact = async () => {
        try {
            const response = await fetch(`${apiUrl}/add-contact`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ id: editingContact, ...updatedContact }),
            });

            if (response.ok) {
                setMessage('Contact updated successfully.');
                fetchPhonebookList(token); // Refresh the list
                setEditingContact(null); // Exit editing mode
                setUpdatedContact({ name: '', phone: '' }); // Clear the form
            } else {
                setMessage('Failed to update contact.');
            }
        } catch (error) {
            setMessage('An error occurred while updating the contact.');
            console.error('Update contact error:', error);
        }
    };

    const handleDeleteContact = async (id) => {
        try {
            const response = await fetch(`${apiUrl}/add-contact`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ id }),
            });

            if (response.ok) {
                setMessage('Contact deleted successfully.');
                fetchPhonebookList(token); // Refresh the list
            } else {
                setMessage('Failed to delete contact.');
            }
        } catch (error) {
            setMessage('An error occurred while deleting the contact.');
            console.error('Delete contact error:', error);
        }
    };

    const handleShareContact = async (contactId) => {
        try {
            const response = await fetch(`${apiUrl}/share-contact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ id: contactId, email: shareEmail }),
            });

            if (response.ok) {
                setMessage('Contact shared successfully.');
                setShareEmail(''); // Clear the share email field
            } else {
                setMessage('Failed to share contact.');
            }
        } catch (error) {
            setMessage('An error occurred while sharing the contact.');
            console.error('Share contact error:', error);
        }
    };

    const handleUnshareContact = async (contactId) => {
        try {
            const response = await fetch(`${apiUrl}/share-contact`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ id: contactId, email: shareEmail }),
            });

            if (response.ok) {
                setMessage('Contact unshared successfully.');
                setShareEmail(''); // Clear the share email field
            } else {
                setMessage('Failed to unshare contact.');
            }
        } catch (error) {
            setMessage('An error occurred while unsharing the contact.');
            console.error('Unshare contact error:', error);
        }
    };


    return (
        <div style={{ textAlign: 'center', marginTop: '50px' }}>
            {!token ? (
                <>
                    <h1>Login</h1>
                    <form onSubmit={handleLogin}>
                        <div>
                            <label>
                                Username:
                                <input
                                    type="text"
                                    value={username}
                                    onChange={(e) => setUsername(e.target.value)}
                                    required
                                />
                            </label>
                        </div>
                        <div>
                            <label>
                                Password:
                                <input
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    required
                                />
                            </label>
                        </div>
                        <button type="submit">Login</button>
                    </form>
                </>
            ) : (
                <>
                    <h1>Phonebook List</h1>
                    <div>
                        <h3>Add Contact</h3>
                        <input
                            type="text"
                            placeholder="Name"
                            value={newContact.name}
                            onChange={(e) =>
                                setNewContact((prev) => ({ ...prev, name: e.target.value }))
                            }
                        />
                        <input
                            type="text"
                            placeholder="Phone"
                            value={newContact.phone}
                            onChange={(e) =>
                                setNewContact((prev) => ({ ...prev, phone: e.target.value }))
                            }
                        />
                        <button onClick={handleAddContact}>Add</button>
                    </div>
                    <ul>
                        {phonebookList.map((contact) => (
                            <li key={contact.id}>
                                {contact.name}: {contact.phone}
                                <button onClick={() => setEditingContact(contact.id)}>Edit</button>
                                <button onClick={() => handleDeleteContact(contact.id)}>
                                    Delete
                                </button>
                                <div>
                                    <input
                                        type="email"
                                        placeholder="Share with Email"
                                        value={shareEmail}
                                        onChange={(e) => setShareEmail(e.target.value)}
                                    />
                                    <button onClick={() => handleShareContact(contact.id)}>Share</button>
                                    <button onClick={() => handleUnshareContact(contact.id)}>Unshare</button>
                                </div>
                            </li>
                        ))}
                    </ul>
                    {editingContact && (
                        <div>
                            <h3>Update Contact</h3>
                            <input
                                type="text"
                                placeholder="Name"
                                value={updatedContact.name}
                                onChange={(e) =>
                                    setUpdatedContact((prev) => ({ ...prev, name: e.target.value }))
                                }
                            />
                            <input
                                type="text"
                                placeholder="Phone"
                                value={updatedContact.phone}
                                onChange={(e) =>
                                    setUpdatedContact((prev) => ({ ...prev, phone: e.target.value }))
                                }
                            />
                            <button onClick={handleUpdateContact}>Update</button>
                            <button onClick={() => setEditingContact(null)}>Cancel</button>
                        </div>
                    )}
                </>
            )}
            {message && <p>{message}</p>}
        </div>
    );
};

export default App;
