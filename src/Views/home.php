<style>
    .autocomplete-container {
        position: relative;
    }
    .autocomplete-suggestions {
        position: absolute;
        border: 1px solid #ddd;
        border-top: none;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        max-height: 200px;
        overflow-y: auto;
    }
    .autocomplete-suggestion {
        padding: 10px;
        cursor: pointer;
    }
    .autocomplete-suggestion:hover {
        background-color: #f2f2f2;
    }
</style>

<div class="hero-section rounded-3 mb-5">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bolder">Hayalindeki Yolculuğa Çık</h1>
        <p class="lead fw-normal mb-0">Türkiye'nin dört bir yanına en uygun otobüs biletlerini kolayca bul ve satın al.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xl-10 mx-auto">
            <div class="card shadow-lg border-0" style="margin-top: -100px;">
                <div class="card-body p-4 p-md-5">
                    <h3 class="text-center mb-4 text-dark"><i class="bi bi-search me-2"></i>Otobüs Seferi Ara</h3>
                    <form action="/sibervatanbilet/public/trip/search" method="POST">
                        <div class="row gx-4 gy-3 align-items-end">
                            <div class="col-md-6 col-lg-3 autocomplete-container">
                                <label for="departure" class="form-label fw-bold">Nereden</label>
                                <input type="text" class="form-control form-control-lg" id="departure" name="departure_city" placeholder="Örn: İstanbul" required autocomplete="off">
                                <div class="autocomplete-suggestions" id="departure-suggestions"></div>
                            </div>
                            <div class="col-md-6 col-lg-3 autocomplete-container">
                                <label for="destination" class="form-label fw-bold">Nereye</label>
                                <input type="text" class="form-control form-control-lg" id="destination" name="destination_city" placeholder="Örn: Ankara" required autocomplete="off">
                                <div class="autocomplete-suggestions" id="destination-suggestions"></div>
                            </div>
                            <div class="col-lg-4">
                                <label for="date" class="form-label fw-bold">Yolculuk Tarihi</label>
                                <input type="date" class="form-control form-control-lg" id="date" name="trip_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-danger btn-lg w-100">Bileti Bul</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cities = <?php echo json_encode($cities); ?>;

    function setupAutocomplete(inputId, suggestionsId) {
        const input = document.getElementById(inputId);
        const suggestions = document.getElementById(suggestionsId);

        input.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            suggestions.innerHTML = '';
            if (!value) {
                suggestions.style.display = 'none';
                return;
            }

            const filteredCities = cities.filter(city => city.toLowerCase().startsWith(value));
            
            if (filteredCities.length > 0) {
                suggestions.style.display = 'block';
                filteredCities.forEach(city => {
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.textContent = city;
                    suggestionDiv.classList.add('autocomplete-suggestion');
                    suggestionDiv.addEventListener('click', function() {
                        input.value = this.textContent;
                        suggestions.innerHTML = '';
                        suggestions.style.display = 'none';
                    });
                    suggestions.appendChild(suggestionDiv);
                });
            } else {
                suggestions.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target !== input) {
                suggestions.innerHTML = '';
                suggestions.style.display = 'none';
            }
        });
    }

    setupAutocomplete('departure', 'departure-suggestions');
    setupAutocomplete('destination', 'destination-suggestions');
});
</script>