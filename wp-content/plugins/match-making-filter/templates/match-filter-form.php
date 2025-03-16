<div class="match-container">
    <!-- Filter Section (Left Side) -->
    <div class="filter-section">
        <h3>Find Your Match</h3>
        <form id="match-filter-form">
            <label>Age Range:</label>
            <input type="number" name="age_min" min="18" max="100" value="18">
            <input type="number" name="age_max" min="18" max="100" value="100">

            <label>Gender:</label>
            <select name="gender">
                <option value="">Any</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="others">Others</option>
            </select>

            <label>Country:</label>
            <input type="text" name="country">

            <label>Relationship Type:</label>
            <select name="relationship_type">
                <option value="">Any</option>
                <option value="casual">Casual</option>
                <option value="long term">Long Term</option>
            </select>

            <button type="submit">Find Matches</button>
        </form>
    </div>

    <!-- Match Results Section (Right Side) -->
    <div class="results-section">
        <div id="match-results">
            <p>Search for matches...</p>
        </div>
    </div>
</div>