/**
 * AV Controls for Just Add Power Receivers
 * 
 * This script handles the client-side interactions for the AV Controls interface
 * designed for Just Add Power receivers. It manages volume control updates and
 * form submissions for controlling the receivers.
 *
 * @author Seth Morrow
 * @version 1.1
 */

/**
 * Updates the volume label next to the slider in real-time as the user adjusts it.
 * 
 * @param {HTMLElement} slider - The volume slider input element
 */
function updateVolumeLabel(slider) {
    // Find the associated label element within the same parent container
    const label = slider.parentElement.querySelector('.volume-label');
    // Update the label text with the current slider value
    label.textContent = slider.value;
}

// Wait for the DOM to be fully loaded before attaching event listeners
$(document).ready(function() {
    // Attach a submit event handler to all forms within elements with class 'receiver'
    $('.receiver form').on('submit', function(e) {
        // Prevent the default form submission behavior
        e.preventDefault();

        // Store a reference to the submitted form
        const form = $(this);

        // Send an AJAX request to handle the form submission
        $.ajax({
            url: '', // The empty string means the form will submit to the current page
            type: 'POST', // Use POST method for form submission
            data: form.serialize(), // Serialize the form data for submission
            dataType: 'json', // Expect a JSON response from the server

            // Handle successful response from the server
            success: function(response) {
                // Get the message display element
                const messageDiv = $('#response-message');

                // Remove existing classes and add appropriate class based on the response
                messageDiv.removeClass('success error').addClass(response.success ? 'success' : 'error');

                // Set the response message text and display it
                messageDiv.text(response.message).show();

                // Hide the message after 5 seconds
                setTimeout(function() {
                    messageDiv.hide();
                }, 5000);
            },

            // Handle errors in the AJAX request
            error: function() {
                // Display an error message if the request fails
                $('#response-message').removeClass('success').addClass('error')
                    .text("An error occurred. Please try again.").show();
            }
        });
    });
});
