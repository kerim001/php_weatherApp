<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cities";

try {
    // PDO kullanarak veri tabanı bağlantısı oluşturma
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['q'])) {
        $q = $_GET['q'];

        // Türkçe karakterleri İngilizce karakterlerle değiştiren fonksiyon
        function normalizeString($str) {
            $charMap = [
                'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
                'Ç' => 'C', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U'
            ];
            return strtr($str, $charMap); // Karakterleri değiştirmek için strtr fonksiyonunu kullanır
        }

        $normalized_q = normalizeString($q) . '%'; 
        // Kullanıcı girdisini normalize eder ve sonuna % ekler (LIKE ifadesi için)

        // prepared statement kullanarak SQL sorgusu
        $stmt = $conn->prepare("SELECT name, id FROM sehirler WHERE id IN (SELECT MIN(id) FROM sehirler WHERE name LIKE :normalized_q  GROUP BY name) ORDER BY name LIMIT 3;");
        $stmt->bindParam(':normalized_q', $normalized_q, PDO::PARAM_STR); // Parametreyi bağlar
        $stmt->execute(); // Sorguyu çalıştırır

        $cities = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cities[] = ['name' => $row['name'],'id'=> $row['id']]; // Şehir isimlerini diziye ekler
        }

        echo json_encode($cities); // Şehir isimlerini JSON formatında çıktı olarak verir
    }
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage(); // Hata mesajı verir
}

$conn = null; // Veritabanı bağlantısını kapatır
?>
