<form id="dating-registration-form" enctype="multipart/form-data">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>

    <div>
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required min="18" max="100">
    </div>

    <div>
        <label for="height">Height (cm):</label>
        <input type="text" id="height" name="height" required>
    </div>

    <div>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male" selected>Male</option>
            <option value="female">Female</option>
            <option value="others">Others</option>
        </select>
    </div>

    <div>
        <label for="interested_in">Interested In:</label>
        <select id="interested_in" name="interested_in" required>
            <option value="male" selected>Male</option>
            <option value="female">Female</option>
            <option value="others">Others</option>
        </select>
    </div>

    <div>
        <label for="country">Country:</label>
        <input type="text" id="country" name="country" required>
    </div>

    <div>
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required pattern="\d{10}" placeholder="Enter 10-digit phone number">
    </div>

    <div>
        <label for="profile_image">Profile Image:</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/*">
    </div>

    <div>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
    </div>

    <div>
        <label for="likes">Likes:</label>
        <input type="text" id="likes" name="likes">
    </div>

    <div>
        <label for="income">Income (Optional):</label>
        <input type="text" id="income" name="income">
    </div>

    <div>
        <label for="zodiac">Zodiac Sign (Optional):</label>
        <input type="text" id="zodiac" name="zodiac">
    </div>

    <div>
        <label for="relationship_type">Relationship Type:</label>
        <select id="relationship_type" name="relationship_type" required>
            <option value="casual" selected>Casual</option>
            <option value="long term">Long Term</option>
        </select>
    </div>

    <button type="submit">Register</button>
</form>

<div id="form-message"></div>

<script>
document.getElementById("dating-registration-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevents default form submission

    let formData = new FormData(this);
    formData.append("action", "dating_handle_registration"); // Ensure action parameter is included

    fetch(dating_ajax.ajax_url, {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Ensure response is JSON
    .then(data => {
        console.log("Server Response:", data); // Debugging log
        let messageDiv = document.getElementById("form-message");
        messageDiv.innerHTML = data.message || "Unexpected response!";
        messageDiv.style.color = data.success ? "green" : "red";
    })
    .catch(error => {
        console.error("Error:", error);
        document.getElementById("form-message").innerHTML = "Something went wrong! Please try again.";
    });
});

</script>
