<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Ongkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            margin-top: 0;
            color: #4CAF50;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        select,
        input[type="number"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Cek Ongkir</h1>
        <form action="cekongkir.php" method="post">
            <label for="origin">Kota Asal:</label>
            <select id="origin" name="origin" required>

                <?php

                $apiKey = '77a92e2fa2a2e479a248a108210062ec';
                $cityUrl = 'https://api.rajaongkir.com/starter/city';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $cityUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('key: ' . $apiKey));
                $cityResponse = curl_exec($ch);
                if (curl_errno($ch)) {
                    die('CURL Error: ' . curl_error($ch));
                }
                curl_close($ch);

                $cityData = json_decode($cityResponse, true);


                if (!isset($cityData['rajaongkir']['results'])) {
                    die('Gagal mengambil data kota.');
                }


                foreach ($cityData['rajaongkir']['results'] as $city) {
                    echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
                }
                ?>
            </select>
            <br>
            <label for="destination">Kota Tujuan:</label>
            <select id="destination" name="destination" required>

                <?php

                foreach ($cityData['rajaongkir']['results'] as $city) {
                    echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
                }
                ?>
            </select>
            <br>
            <label for="weight">Berat (gram):</label>
            <input type="number" id="weight" name="weight" required>
            <br>
            <input type="submit" value="Cek Ongkir">
        </form>
    </div>
</body>

</html>