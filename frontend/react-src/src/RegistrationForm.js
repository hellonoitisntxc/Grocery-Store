import React, { useState, useEffect } from 'react';
import './RegistrationForm.css'; // Create a CSS file for styling

function RegistrationForm() {
    const [formData, setFormData] = useState({
        name: '',
        phone: '',
        email: '',
        password: '',
    });

    const [errors, setErrors] = useState({}); // For validation errors
    const [submitStatus, setSubmitStatus] = useState({ // For submission feedback
        message: '',
        type: '', // 'success' or 'error'
    });
    const [isSubmitting, setIsSubmitting] = useState(false); // To disable button

    // --- Live Validation Logic ---
    const validateField = (name, value) => {
        let error = '';
        switch (name) {
            case 'name':
                if (!value) {
                    error = 'Name is required.';
                } else if (!/^[a-zA-Z\s]+$/.test(value)) {
                    error = 'Name can only contain letters and spaces.';
                }
                break;
            case 'phone':
                if (!value) {
                    error = 'Phone number is required.';
                } else if (!/^\d{10}$/.test(value)) {
                    error = 'Phone number must be exactly 10 digits.';
                }
                break;
            case 'email':
                if (!value) {
                    error = 'Email is required.';
                } else if (!/\S+@\S+\.\S+/.test(value)) { // Simple email regex
                    error = 'Email address is invalid.';
                }
                break;
            case 'password':
                if (!value) {
                    error = 'Password is required.';
                } else if (value.length < 6) {
                    error = 'Password must be at least 6 characters long.';
                }
                break;
            default:
                break;
        }
        return error;
    };

    // --- Handle Input Changes ---
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prevState => ({
            ...prevState,
            [name]: value,
        }));

        // Perform live validation on keystroke
        const error = validateField(name, value);
        setErrors(prevErrors => ({
            ...prevErrors,
            [name]: error, // Update or clear the error for this field
        }));
    };

    // --- Handle Form Submission ---
    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitStatus({ message: '', type: '' }); // Clear previous submit message

        // Perform final validation on all fields before submitting
        let formIsValid = true;
        const finalErrors = {};
        Object.keys(formData).forEach(fieldName => {
            const error = validateField(fieldName, formData[fieldName]);
            if (error) {
                finalErrors[fieldName] = error;
                formIsValid = false;
            }
        });

        setErrors(finalErrors);

        if (!formIsValid) {
            setSubmitStatus({ message: 'Please fix the errors above.', type: 'error' });
            return; // Don't submit if validation fails
        }

        setIsSubmitting(true); // Disable button
        setSubmitStatus({ message: 'Registering...', type: '' });

        try {
            const response = await fetch('../../backend/register.php', { // Path relative to where HTML is served
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                setSubmitStatus({ message: result.message || 'Registration successful! Redirecting...', type: 'success' });
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = result.redirectUrl || '../public/index.html'; // Use redirect URL from backend
                }, 2000);
            } else {
                // Handle errors from backend (like duplicate email, validation fails)
                setSubmitStatus({ message: result.message || `Registration failed (Status: ${response.status})`, type: 'error' });
                setIsSubmitting(false); // Re-enable button
            }

        } catch (error) {
            console.error("Submission error:", error);
            setSubmitStatus({ message: 'An unexpected error occurred. Please try again.', type: 'error' });
            setIsSubmitting(false); // Re-enable button
        }
    };

    return (
        <div className="registration-container">
            <h2>Create Account</h2>
            <form onSubmit={handleSubmit} noValidate> {/* noValidate disables browser default validation */}
                <div className="form-group">
                    <label htmlFor="name">Name:</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value={formData.name}
                        onChange={handleChange}
                        aria-invalid={!!errors.name}
                        aria-describedby="name-error"
                    />
                    {errors.name && <p id="name-error" className="error-text">{errors.name}</p>}
                </div>

                <div className="form-group">
                    <label htmlFor="phone">Phone Number (10 digits):</label>
                    <input
                        type="tel" // Use 'tel' type for phone numbers
                        id="phone"
                        name="phone"
                        value={formData.phone}
                        onChange={handleChange}
                        maxLength="10" // Help user input
                        aria-invalid={!!errors.phone}
                        aria-describedby="phone-error"
                    />
                    {errors.phone && <p id="phone-error" className="error-text">{errors.phone}</p>}
                </div>

                <div className="form-group">
                    <label htmlFor="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        aria-invalid={!!errors.email}
                        aria-describedby="email-error"
                    />
                    {errors.email && <p id="email-error" className="error-text">{errors.email}</p>}
                </div>

                <div className="form-group">
                    <label htmlFor="password">Password (min 6 chars):</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        value={formData.password}
                        onChange={handleChange}
                        aria-invalid={!!errors.password}
                        aria-describedby="password-error"
                    />
                    {errors.password && <p id="password-error" className="error-text">{errors.password}</p>}
                </div>

                <button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? 'Registering...' : 'Register'}
                </button>

                {/* Submission Status Message */}
                {submitStatus.message && (
                    <p className={`submit-message ${submitStatus.type}`}>
                        {submitStatus.message}
                    </p>
                )}
            </form>
            <div className="login-link">
                Already have an account? <a href="login.html">Login here</a>
            </div>
        </div>
    );
}

export default RegistrationForm;