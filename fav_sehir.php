<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cities";

// Kullanıcının IP adresini alma
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['q'])) {
        $city_name = $_GET['q'];
        // Sehirler tablosundan city_id değerini almak için sorguyu çalıştırıyoruz
        $stmt = $conn->prepare("SELECT id FROM sehirler WHERE name = :city_name ORDER BY id ASC LIMIT 1");
        $stmt->bindParam(':city_name', $city_name, PDO::PARAM_STR);
        $stmt->execute();

        $city_id = $stmt->fetchColumn(); // id değerini alıyoruz
        // Hazırlıklı ifade kullanarak veri ekleme
        $stmt = $conn->prepare("INSERT INTO FavCity (id, user_id, city_name) VALUES (:id, :user_id, :city_name)");
        $stmt->bindParam(':id', $city_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $ip_address, PDO::PARAM_STR);
        $stmt->bindParam(':city_name', $city_name, PDO::PARAM_STR);
        $stmt->execute();

        echo "Favori şehir başarıyla eklendi!";
    } else {
        // Kullanıcının IP'sine göre favori şehirleri getirir, sadece birini seçiyoruz
        $stmt = $conn->prepare("SELECT city_name FROM FavCity ");
        $stmt->execute(); // Sorguyu çalıştırır

        $cities = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cities[] = ['city_name' => $row['city_name']]; // Şehir isimlerini diziye ekler
        }
        echo json_encode($cities); // Şehir isimlerini JSON formatında çıktı olarak verir
    }
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

$conn = null;
?>
