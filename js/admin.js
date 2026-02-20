function showSection(section) {
    document.getElementById('films').style.display = 'none';
    document.getElementById('members').style.display = 'none';
    document.getElementById('active-rentals').style.display = 'none';
    document.getElementById('all-rentals').style.display = 'none';
    document.getElementById('add').style.display = 'none';
    document.getElementById(section).style.display = 'block';
}

function deleteFilm(id) {
    if(confirm('Are you sure you want to delete this film?')) {
        fetch('../api/films.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'delete', film_id: id})
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Film deleted successfully!');
                    var row = document.getElementById('film-' + id);
                    if(row) row.remove();
                } else {
                    alert('Error deleting film');
                }
            });
    }
}

function deleteMember(id) {
    if(confirm('Are you sure you want to delete this member?')) {
        fetch('../api/members.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'delete', id: id})
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Member deleted successfully!');
                    var row = document.getElementById('member-' + id);
                    if(row) row.remove();
                } else {
                    alert('Error deleting member');
                }
            });
    }
}

function returnFilm(rentalId) {
    if(confirm('Mark this film as returned by admin?')) {
        fetch('../api/rentals.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'return_admin', rental_id: rentalId})
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Film marked as returned by admin!');
                    var row = document.getElementById('rental-' + rentalId);
                    if(row) row.remove();
                } else {
                    alert('Error processing return');
                }
            });
    }
}

function searchOMDb() {
    var searchTerm = document.getElementById('omdbSearch').value;

    if(!searchTerm) {
        alert('Please enter a movie title to search');
        return;
    }

    var resultsDiv = document.getElementById('omdbResults');
    resultsDiv.innerHTML = '<p>Searching...</p>';

    fetch('https://www.omdbapi.com/?apikey=793d01d0&s=' + searchTerm)
        .then(response => response.json())
        .then(data => {
            if(data.Response === 'True') {
                displayOMDbResults(data.Search);
            } else {
                resultsDiv.innerHTML = '<p>No results found. Try a different search term.</p>';
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = '<p>Error searching. Please try again.</p>';
            console.error('Error:', error);
        });
}

function displayOMDbResults(movies) {
    var container = document.getElementById('omdbResults');
    container.innerHTML = '';

    movies.forEach(function(movie) {
        var movieDiv = document.createElement('div');
        movieDiv.style.cssText = 'background: #333; padding: 15px; margin: 10px 0; display: flex; gap: 15px; border-radius: 5px;';

        var posterSrc = movie.Poster !== 'N/A' ? movie.Poster : 'https://via.placeholder.com/100x150';

        movieDiv.innerHTML =
            '<img src="' + posterSrc + '" style="width: 100px; height: 150px; object-fit: cover;">' +
            '<div style="flex: 1;">' +
            '<h3 style="margin: 0 0 10px 0;">' + movie.Title + ' (' + movie.Year + ')</h3>' +
            '<p>Type: ' + movie.Type + '</p>' +
            '<p>IMDB ID: ' + movie.imdbID + '</p>' +
            '<button class="btn" onclick="addFromOMDb(\'' + movie.imdbID + '\')">Add to Database</button>' +
            '</div>';

        container.appendChild(movieDiv);
    });
}

function addFromOMDb(imdbId) {
    fetch('https://www.omdbapi.com/?apikey=793d01d0&i=' + imdbId)
        .then(response => response.json())
        .then(data => {
            if(data.Response === 'True') {
                var filmData = {
                    action: 'add',
                    title: data.Title,
                    year: parseInt(data.Year) || 2024,
                    genre: data.Genre ? data.Genre.split(',')[0].trim() : 'Unknown',
                    director: data.Director || 'Unknown',
                    actors: data.Actors || '',  
                    rating: parseFloat(data.imdbRating) || 0,
                    poster: data.Poster !== 'N/A' ? data.Poster : '',
                    description: data.Plot || 'No description available',
                    imdb_id: data.imdbID
                };

                return fetch('../api/films.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(filmData)
                });
            } else {
                throw new Error('Failed to get movie details');
            }
        })
        .then(response => response.json())
        .then(result => {
            if(result.success) {
                alert('Film added successfully with actors information!');
                window.location.reload();
            } else {
                alert(result.message || 'Error adding film');
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            console.error('Error:', error);
        });
}