<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather-App</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"></head>
<body class="bg-dark">
    <div class="card">
        <div class="search">
            <input type="text" placeholder="şehir adı giriniz" spellcheck="false" id="cityInput" autocomplete="off" oninput="suggestCities()"> 
            <button><img src="images/search.png"></button> 
        </div>
        <div class="sListe">
            <ul id="suggestions"></ul>
        </div>
        <div class="error">
            <p>Bilinmeyen şehir ismi</p>
        </div>
        <div class="header">
                <button class="headerbutton" id="starButton"><i id="StarIcon" class="fa-regular fa-star">   Favoriler</i></button>
                <ul id ="FavCityList"></ul>
            </div>
        <div class="weather">
            
            <img src="images/rain.png" class="weather-icon">
            <h1 class="temp" id="isi">40°C</h1>
            <h2 class="city" id="sehir">Adana</h2>
            <p>
                <button class="btn btn-primary text-start" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Detaylar
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="mcard card-body">
                    <div class="details">
                        <div class="col">
                            <img src="images/humidity.png">
                            <div>
                                <p class="humidity">50%</p>
                                <p>Nem</p>
                            </div>
                        </div>
                        <div class="col">
                            <img src="images/wind.png">
                            <div>
                                <p class="wind">15 km/h</p>
                                <p>Rüzgar Hızı</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="HomeButton">
                <button class="btn btn-primary text-start" type="button" id="FavCity">
                    <i class="fa fa-home"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <script>
        const MAX_SUGGESTIONS = 3;
        const apiKey = "7a067376bf61bbc1eead0b0394ba6ea2";
        const apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric&q=";
        const searchBox = document.querySelector(".search input");
        const searchBtn = document.querySelector(".search button");
        const weatherIcon = document.querySelector(".weather-icon");
        const cityInput = document.getElementById('cityInput');
        const suggestionsList = document.getElementById('suggestions');
        const isimler = document.getElementById('FavCityList');
        var StarButton = document.getElementById('starButton');
        var starButtonIcon = document.getElementById('StarIcon')
        var HomeButton = document.querySelector(".HomeButton button");

        HomeButton.addEventListener('click', function() {
            var favCity = localStorage.getItem('favCity');
            checkWeather(favCity);
        });

        StarButton.addEventListener('click', function() {
            const FavOlacakSehir = document.querySelector('.search input').value;
            const sallamaID = document.getElementById('isi').textContent;
            const xhr =new XMLHttpRequest();
            xhr.open('GET',`fav_sehir.php?q=${FavOlacakSehir}`,true);
            // get isteğimizi açtık ve fav eklenecek şehri sorgu parametresi olarak atayacağız
            alert("bu şehri favori olarak eklediniz");
            xhr.send();
            });

    window.onload = function() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fav_sehir.php', true); // fav_sehir.php'ye GET isteği gönderiyoruz
        xhr.onload = function() {
                if (this.status === 200) {
                    const favoriler = JSON.parse(this.responseText); // Sunucudan gelen yanıtı JSON formatında çözüyoruz
                    if (favoriler.length > 0) {
                        favoriler.forEach(isim =>{
                            const li = document.createElement('li');
                            li.textContent = isim.city_name;
                        
                            li.addEventListener('click', () => {
                            checkWeather(isim.city_name); // Tıklanan şehir için hava durumu bilgisini getiriyoruz
                            starButtonIcon.style.fontWeight = "600";
                            });

                            isimler.appendChild(li); // Liste elemanını öneri listesine ekler
                        })
                    }
                    
                }
        };
    xhr.send();
};


function suggestCities() {
    // Kullanıcının girdiği metni normalize eder (Türkçe karakterleri İngilizce karakterlere çevirir)
    const inputText = normalizeString(cityInput.value.trim().toLowerCase());
    
    // Eğer giriş boşsa, öneri listesini temizler ve işlemi sonlandırır
    if (inputText === '') {
        suggestionsList.innerHTML = ''; // Öneri listesini temizler
        return;
    }
    
    // XMLHttpRequest kullanarak AJAX isteği oluşturur
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `suggest_cities.php?q=${inputText}`, true); 
    // GET isteği açar ve inputText'i sorgu parametresi olarak ekler
    xhr.onload = function() {
        // İstek başarıyla tamamlandığında çalışır
        if (this.status === 200) {
            // Sunucudan dönen JSON verisini çözer
            const suggestions = JSON.parse(this.responseText);
            suggestionsList.innerHTML = ''; // Öneri listesini temizler
            
            // Gelen öneri şehir isimleri için liste elemanları oluşturur
            suggestions.forEach(city => {
                const li = document.createElement('li'); // Yeni bir liste elemanı oluşturur
                li.textContent = city.name; // Liste elemanına şehir ismini ekler
                
                // Liste elemanına tıklandığında çalışacak olay dinleyicisi ekler
                li.addEventListener('click', () => {
                    cityInput.value = city.name; // Şehir ismini giriş alanına yazar
                    checkWeather(city.name); // Seçilen şehir için hava durumunu kontrol eder
                    suggestionsList.innerHTML = ''; // Öneri listesini temizler
                });
                
                suggestionsList.appendChild(li); // Liste elemanını öneri listesine ekler
            });
        }
    };
    xhr.send(); // AJAX isteğini gönderir
}

function normalizeString(metin) {
    const charMap = {
        'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
        'Ç': 'C', 'Ğ': 'G', 'İ': 'I', 'Ö': 'O', 'Ş': 'S', 'Ü': 'U'
    };
    return metin.replace(/[çğışöüÇĞİŞÖÜ]/g, char => charMap[char]);
}



        async function checkWeather(city) {
            const response = await fetch(apiUrl + city + `&appid=${apiKey}`);
            if (response.status == 404) {
                document.querySelector(".error").style.display = "block";
                document.querySelector(".weather").style.display = "none";
            } else {
                var data = await response.json();
                document.querySelector(".city").innerHTML = data.name;
                document.querySelector(".temp").innerHTML = Math.round(data.main.temp) + "°C";
                document.querySelector(".humidity").innerHTML = data.main.humidity + "%";
                document.querySelector(".wind").innerHTML = data.wind.speed + " km/h";

                if (data.weather[0].main == "Clouds") {
                    weatherIcon.src = "images/clouds.png";
                } else if (data.weather[0].main == "Clear") {
                    weatherIcon.src = "images/clear.png";
                } else if (data.weather[0].main == "Rain") {
                    weatherIcon.src = "images/rain.png";
                } else if (data.weather[0].main == "Drizzle") {
                    weatherIcon.src = "images/drizzle.png";
                } else if (data.weather[0].main == "Mist") {
                    weatherIcon.src = "images/mist.png";
                }

                document.querySelector(".weather").style.display = "block";
                document.querySelector(".error").style.display = "none";

                if (localStorage.getItem("favCity").trim().toLocaleLowerCase() === city.trim().toLocaleLowerCase()) {
                    starButtonIcon.style.fontWeight = "600";
                } else {
                    starButtonIcon.style.fontWeight = "400";
                    searchBox.value = '';
                }
            }
        }

        searchBtn.addEventListener("click", () => {
            checkWeather(searchBox.value);
            suggestionsList.innerHTML = '';
        });

        searchBox.addEventListener('keydown', function(event) {
            if (event.key == 'Enter') {
                checkWeather(searchBox.value);
                suggestionsList.innerHTML = '';
            }
        });
    </script>
</body>
</html>
