function rentFilm(id) {
    fetch('../api/rentals.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'rent', film_id: id})
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Film rented!');
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => {
            alert('Error renting film');
            console.error('Error:', error);
        });
}

function addToWatchlist(id) {
    fetch('../api/films.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'watchlist', film_id: id})
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Added to watchlist!');
        })
        .catch(error => {
            alert('Error adding to watchlist');
            console.error('Error:', error);
        });
}

function removeFromWatchlist(filmId) {
    if(confirm('Remove this film from your watchlist?')) {
        fetch('../api/films.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'remove_watchlist', film_id: filmId})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Removed from watchlist!');
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => {
            alert('Error removing from watchlist');
            console.error('Error:', error);
        });
    }
}

function toggleReviewForm() {
    var form = document.getElementById('reviewForm');
    if(form) {
        if(form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
}

function validateForm(formId) {
    var form = document.getElementById(formId);
    if(!form) return true;

    var inputs = form.getElementsByTagName('input');

    for(var i = 0; i < inputs.length; i++) {
        if(inputs[i].hasAttribute('required') && inputs[i].value === '') {
            alert('Please fill all required fields');
            inputs[i].focus();
            return false;
        }
    }
    return true;
}

window.onload = function() {
    if(!document.cookie.includes('cookiesAccepted=true')) { 
        var notice = document.createElement('div');
        notice.id = 'cookieNotice';
        notice.style.cssText = 'position: fixed; bottom: 0; width: 100%; background: #333; color: white; padding: 15px; text-align: center; z-index: 9999;';
        notice.innerHTML = 'This website uses cookies to improve your experience. ' +
            '<button onclick="acceptCookies()" style="background: #e50914; color: white; border: none; padding: 5px 15px; margin-left: 10px; cursor: pointer;">Accept</button>';
        document.body.appendChild(notice);
    }
};

function acceptCookies() {
    document.cookie = "cookiesAccepted=true; max-age=31536000; path=/";
    var notice = document.getElementById('cookieNotice');
    if(notice) {
        notice.remove();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('username');
    const feedback = document.getElementById('username-feedback');

    if(!usernameInput || !feedback) {
        return;
    }

    let checkTimer;

    usernameInput.addEventListener('keyup', function() {
        clearTimeout(checkTimer);
        const username = this.value;

        if(username.length < 3) {
            feedback.innerHTML = '';
            feedback.style.display = 'none';
            return;
        }

        feedback.style.display = 'block';
        feedback.innerHTML = 'Checking...';
        feedback.className = 'username-feedback username-checking';

        checkTimer = setTimeout(() => {
            fetch('../api/check_username.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({username: username})
            })
                .then(response => response.json())
                .then(data => {
                    if(data.available) {
                        feedback.innerHTML = '✓ Username available';
                        feedback.className = 'username-feedback username-available';
                    } else {
                        feedback.innerHTML = '✗ Username already taken';
                        feedback.className = 'username-feedback username-taken';
                    }
                })
                .catch(error => {
                    console.error('Error checking username:', error);
                    feedback.innerHTML = 'Error checking username';
                    feedback.className = 'username-feedback username-taken';
                });
        }, 500);
    });
});