<?php

$apiKey = '77a92e2fa2a2e479a248a108210062ec';
$costUrl = 'https://api.rajaongkir.com/starter/cost';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $originCode = isset($_POST['origin']) ? trim($_POST['origin']) : '';
    $destinationCode = isset($_POST['destination']) ? trim($_POST['destination']) : '';
    $weight = isset($_POST['weight']) ? trim($_POST['weight']) : '';

    // Validasi input
    if (empty($originCode) || empty($destinationCode) || empty($weight)) {
        die('Semua field harus diisi.');
    }

    // Data yang akan dikirim ke API
    $data = array(
        'origin' => $originCode,
        'destination' => $destinationCode,
        'weight' => $weight,
        'courier' => 'jne' // Atur kurir sesuai kebutuhan (jne, pos, tiki, dll)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $costUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        'key: ' . $apiKey
    ));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Kesalahan CURL: ' . curl_error($ch));
    }
    curl_close($ch);

    // Decode respon API
    $result = json_decode($response, true);

    // Cek apakah respon valid
    if (!$result || !isset($result['rajaongkir'])) {
        die('Respon API tidak valid.');
    }

    $status = $result['rajaongkir']['status']['code'];
    if ($status != 200) {
        die('Error API: ' . $result['rajaongkir']['status']['description']);
    }

    $originDetails = $result['rajaongkir']['origin_details'] ?? 'Data tidak ditemukan';
    $destinationDetails = $result['rajaongkir']['destination_details'] ?? 'Data tidak ditemukan';
    $costs = $result['rajaongkir']['results'][0]['costs'] ?? [];

} else {
    die('Form belum disubmit.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Cek Ongkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hasil Cek Ongkir</h1>
        <p><strong>Kota Asal:</strong> <?php echo is_array($originDetails) ? $originDetails['city_name'] : $originDetails; ?></p>
        <p><strong>Kota Tujuan:</strong> <?php echo is_array($destinationDetails) ? $destinationDetails['city_name'] : $destinationDetails; ?></p>
        <p><strong>Berat:</strong> <?php echo htmlspecialchars($weight); ?> gram</p>

        <?php if (!empty($costs)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Estimasi (hari)</th>
                        <th>Biaya (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($costs as $cost): ?>
                        <?php foreach ($cost['cost'] as $detail): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cost['service']); ?></td>
                                <td><?php echo htmlspecialchars($detail['etd']); ?></td>
                                <td><?php echo number_format($detail['value'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Biaya pengiriman tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
