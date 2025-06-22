<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rooms and Rates</title>
  <link rel="stylesheet" href="assets/css/style.css"> <!-- sesuaikan path -->
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background: #f2f2f2;
    }
    h1 {
      text-align: center;
      color: #1a2d6d;
      margin-bottom: 40px;
    }
    .room-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }
    .room-card {
      border: 1px solid #ccc;
      width: 250px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      text-align: center;
      transition: transform 0.3s ease;
      background: #fff;
    }
    .room-card:hover {
      transform: translateY(-5px);
    }
    .room-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }
    .room-title {
      background-color: #1a2d6d;
      color: white;
      font-size: 18px;
      padding: 10px 0;
    }
    .rating {
      margin: 10px 0;
      color: #333;
    }
    .price {
      color: red;
      font-weight: bold;
      font-size: 20px;
      margin: 5px 0;
    }
    .book-btn {
      background: #ffcc00;
      border: none;
      padding: 10px 20px;
      font-weight: bold;
      cursor: pointer;
      margin-bottom: 15px;
      border-radius: 5px;
    }
    .book-btn:hover {
      background-color: #ffc107;
    }
  </style>
</head>
<body>

  <h1>Rooms And Rates</h1>

  <div class="room-container">
    <?php
    $rooms = [
      ["img" => "uploads/deluxe.jpeg", "title" => "Luxury Room", "price" => "1.000.000", "stars" => 4],
      ["img" => "uploads/luxury.jpg", "title" => "Deluxe Room", "price" => "750.000", "stars" => 4],
      ["img" => "uploads/superior.jpeg", "title" => "Superior Room", "price" => "500.000", "stars" => 3],
      ["img" => "uploads/single.jpeg", "title" => "Single Room", "price" => "250.000", "stars" => 2],
    ];

    foreach ($rooms as $room) {
      echo '<div class="room-card">';
      echo '<img src="'.$room['img'].'" alt="'.$room['title'].'">';
      echo '<div class="room-title">'.$room['title'].'</div>';
      echo '<div class="rating">'.str_repeat('★', $room['stars']).str_repeat('☆', 5 - $room['stars']).'</div>';
      echo '<div class="price">Rp. '.$room['price'].'</div>';
      echo '<form action="reservation.php" method="get">';
      echo '<input type="hidden" name="room" value="'.$room['title'].'">';
      echo '<button class="book-btn" type="submit">Book Now</button>';
      echo '</form>';
      echo '</div>';
    }
    ?>
  </div>

</body>
</html>
