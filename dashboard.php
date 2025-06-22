<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile_image = 'uploads/' . $user_id . '.jpg';

$username = $_SESSION['username']; // untuk popup
$show_welcome = false;
if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true;
    $show_welcome = true;
}

if (!file_exists($profile_image)) {
    $profile_image = 'uploads/default.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: rgb(245, 245, 240);
    }

    /* Header */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding: 15px 40px;
      background-color: #008080;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
    }

    .branding {
      font-size: 30px;
      font-weight: bold;
      color: white;
    }

    .branding small {
      display: block;
      color: #f5deb3;
      font-size: 12px;
      letter-spacing: 2px;
    }

    .right-nav {
      display: flex;
      align-items: center;
    }

    .nav-links {
      display: flex;
      align-items: center;
    }

    .nav-links a {
      color: white;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
      font-size: 16px;
    }

    .profile-btn {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background-color: #008080;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      border: none;
      margin-left: 20px;
      overflow: hidden;
    }

    .profile-btn img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    /* Hero Section */
    .hero {
      margin-top: 100px;
      position: relative;
      width: 100%;
      height: 100vh;
      background-image: url('uploads/dashboard.jpg');
      background-size: cover;
      background-position: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }

    .hero h1 {
      font-size: 60px;
      font-weight: bold;
      letter-spacing: 10px;
      color: #ffffff;
      margin: 0;
    }

    .hero p {
      font-size: 28px;
      font-weight: 600;
      letter-spacing: 3px;
      color: #008080;
      margin: 20px 0;
    }

    .hero .subtext {
      font-size: 18px;
       font-weight: 600;
      color: #f5deb3;
      letter-spacing: 5px;
    }

    .hero .btn {
      background-color: #008080;
      color: white;
      padding: 12px 28px;
      border-radius: 5px;
      font-size: 16px;
      margin-top: 20px;
      border: none;
      cursor: pointer;
    }

    /*About Section*/
    .about-us {
    padding: 60px 20px;
    text-align: center;
    background-color: #f2f2f2; 
    }

    .about-title {
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 40px;
      color: #008080;
    }

    .about-content {
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      justify-content: center;
      align-items: center;
    }

    .about-image img {
    width: 80%; 
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .about-text {
      flex: 1 1 400px;
      margin-right: 40px;
    }

    .about-text {
      text-align: justify;
      max-width: 600px;
    }

    /*Service*/
    .our-services {
    text-align: center;
    padding: 60px 20px;
    background: #f2f2f2 ;
    color: #008080;
    }


    .our-services h2 {
      font-size: 36px;
      margin-bottom: 40px;
      font-family: 'Segoe UI', sans-serif;
    }

    .services-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    background-color: transparent; /* atau hapus aja */
    padding: 40px 0;
    }

    .service-box {
      flex: 1 1 300px;
      max-width: 400px;
      background-color: #ffffff; /* background putih */
      padding: 30px;
      border-radius: 10px;
      text-align: left;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .service-box .icon {
      font-size: 48px;
      color: #f5deb3; /* #f5deb3 */
      margin-bottom: 20px;
      text-align: center;
    }

    .service-box h3 {
      font-size: 22px;
      margin-bottom: 10px;
      font-weight: bold;
      text-align: center; 
    }

    .service-box p {
      margin-bottom: 15px;
    }

    .service-box ul {
      list-style: none;
      padding-left: 0;
    }

    .service-box li {
      margin-bottom: 10px;
    }

    .service-box li i {
      color: #f5deb3;
      margin-right: 8px;
    }

    /*Contact*/
    .contact-section {
    padding: 60px 20px;
    background-color: #f2f2f2; /* warna lembut mirip services */
    text-align: center;
    }

    .contact-title {
      font-size: 36px;
      font-family: 'Segoe UI', sans-serif;
      font-weight: bold;
      color: #008080;
      margin-bottom: 40px;
    }

    .contact-box {
      max-width: 900px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .about-us {
        flex-direction: column;
        padding: 40px 20px;
      }

      .about-text h2 {
        font-size: 28px;
      }

      .about-text p {
        font-size: 16px;
      }
    }


    
/* Welcome popup */
.welcome-popup {
  position: fixed;
  top: 30px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #f5deb3; /* elegan, cocok dengan teal */
  color: #004d4d; /* teal gelap atau bisa pakai #2c3e50 untuk nuansa netral */
  padding: 15px 30px;
  border-radius: 8px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  z-index: 10000;
  font-size: 16px;
  font-weight: bold;
  animation: fadeInOut 4s ease-in-out forwards;
  opacity: 0;
}


@keyframes fadeInOut {
  0% { opacity: 0; transform: translateX(-50%) translateY(-10px); }
  10% { opacity: 1; transform: translateX(-50%) translateY(0); }
  90% { opacity: 1; }
  100% { opacity: 0; transform: translateX(-50%) translateY(-10px); }
}


/* Responsive basic */
    @media (max-width: 768px) {
      .nav-links a {
        margin: 0 8px;
        font-size: 14px;
      }

      .branding {
        font-size: 24px;
      }

      .hero h1 {
        font-size: 40px;
      }

      .hero p {
        font-size: 20px;
      }

      .hero .subtext {
        font-size: 14px;
      }
    }
  </style>
</head>

<body>
<?php if ($show_welcome): ?>
  <div class="welcome-popup">Selamat datang, <?php echo htmlspecialchars($username); ?>!</div>
<?php endif; ?>


  <!-- Header -->
  <div class="header">
    <div class="branding">
      <span>SESADUL</span>
      <small>STAY IN COMFORT, STAY WITH US.</small>
    </div>
    
    <div class="right-nav">
      <div class="nav-links">
        <a href="#home">HOME</a>
        <a href="#rooms">ROOMS</a>
        <a href="#about">ABOUT</a>
        <a href="#service">SERVICES</a>
        <a href="#contact">CONTACT</a>
        <a href="riwayat_transaksi.php">HISTORY</a>
      </div>
      <div class="profile-btn" onclick="window.location.href='profile.php'">
        <img src="<?php echo $profile_image; ?>" alt="Profile">
      </div>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="hero" id="home">
    <h1>SESADUL HOTEL</h1>
    <p>WANT LUXURIOUS VACATION?</p>
    <div class="subtext">GET ACCOMMODATION TODAY</div>
    <button class="btn" onclick="window.location.href='reservasi.php'">BOOKING NOW</button>
  </div>

<!-- Rooms and Rates Section -->
<div id="rooms" style="padding: 60px 20px; background-color: #f2f2f2;">
  <h1 style="text-align: center; color: #008080; margin-bottom: 40px;">Rooms And Rates</h1>

  <style>
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
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      text-align: center;
      transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
      opacity: 0;
      transform: translateY(40px);
      animation: fadeInUp 0.6s ease forwards;
    }

    .room-card:hover {
      transform: translateY(-10px) scale(1.03);
      box-shadow: 0 12px 25px rgba(0, 128, 128, 0.25);
      background: linear-gradient(135deg, #f0fdfa, #e6f9f9);
      z-index: 2;
    }

    .room-card:nth-child(1) { animation-delay: 0.1s; }
    .room-card:nth-child(2) { animation-delay: 0.2s; }
    .room-card:nth-child(3) { animation-delay: 0.3s; }
    .room-card:nth-child(4) { animation-delay: 0.4s; }
    .room-card:nth-child(5) { animation-delay: 0.5s; }
    .room-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

  <div class="room-container">
    <?php
    include 'db.php';

    $query = "SELECT * FROM rooms ORDER BY position ASC";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($room = mysqli_fetch_assoc($result)) {
        $image = $room['image'];
        $title = $room['room_type'];
        $price = number_format($room['price'], 0, ',', '.');
        $rating = (int) $room['rating'];
        $description = htmlspecialchars($room['description']);
        $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

        echo '<div class="room-card">';
        echo '<img src="uploads/'.$image.'" alt="'.$title.'" style="width:100%;height:160px;object-fit:cover;">';
        echo '<div class="room-title" style="background-color:#008080;color:white;font-size:18px;padding:10px 0;">'.$title.'</div>';
        echo '<div class="price" style="color:red;font-weight:bold;font-size:20px;margin:5px 0;">Rp. '.$price.'</div>';
        echo '<div class="rating" style="color:#FFD700;font-size:18px;margin-bottom:10px;">'.$stars.'</div>';
        echo '<button 
                class="view-details-btn" 
                onclick="showRoomDetails(this)" 
                data-title="'.$title.'" 
                data-price="'.$price.'" 
                data-image="uploads/'.$image.'" 
                data-rating="'.$rating.'" 
                data-description="'.$description.'"
                style="background:#008080;color:white;border:none;padding:10px 20px;font-weight:bold;cursor:pointer;margin-bottom:15px;border-radius:5px;">
                View Details
              </button>';
        echo '</div>';
      }
    } else {
      echo "<p>Tidak ada data kamar tersedia.</p>";
    }
    ?>
  </div>
</div>

<!-- Modal -->
<div id="roomModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
  <div style="background:white; padding:20px; border-radius:10px; max-width:600px; width:90%; position:relative;">
    <span onclick="closeModal()" style="position:absolute; top:10px; right:15px; font-size:20px; font-weight:bold; color:#999; cursor:pointer;">&times;</span>
    
    <h2 id="modalTitle" style="color:#008080; margin-bottom:20px;"></h2>
    
    <div style="display: flex; gap: 20px; align-items: flex-start;">
      <img id="modalImg" src="" alt="" style="width:40%; height:auto; border-radius:8px; flex-shrink: 0;">
      
      <div style="flex: 1;">
        <p id="modalPrice" style="font-size:18px; font-weight:bold; color:red; margin-top:0;"></p>
        <p id="modalStars" style="font-size:16px; color:#333; margin-top:10px;"></p>
        <p id="modalDesc" style="margin-top:15px; font-size:14px; color:#555; line-height:1.4;"></p>
        <button onclick="window.location.href='reservasi.php'" style="margin-top:20px; padding: 10px 20px; background-color:#008080; color:white; border:none; border-radius:5px; cursor:pointer;">
          Book Now
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Script -->
<script>
  function showRoomDetails(button) {
    const title = button.getAttribute('data-title');
    const price = button.getAttribute('data-price');
    const image = button.getAttribute('data-image');
    const rating = parseInt(button.getAttribute('data-rating')) || 0;
    const description = button.getAttribute('data-description');

    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalImg').src = image;
    document.getElementById('modalImg').alt = title;
    document.getElementById('modalPrice').textContent = 'Harga: Rp. ' + price;
    document.getElementById('modalStars').textContent = 'Rating: ' + '★'.repeat(rating) + '☆'.repeat(5 - rating);
    document.getElementById('modalDesc').textContent = description;

    document.getElementById('roomModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('roomModal').style.display = 'none';
  }
</script>



  <!-- About Us Section -->
  <div class="about-us" id="about">
    <!-- Judul di atas -->
    <h2 class="about-title">ABOUT SESADUL</h2>

    <!-- Kontainer dua kolom -->
    <div class="about-content">
      <div class="about-image">
        <img src="uploads/about.jpg" alt="About Sesadul Hotel">
      </div>
      <div class="about-text">
        <p>Welcome to Sesadul Hotel, where luxury and comfort meet in perfect harmony. Nestled in the heart of the city, Sesadul Hotel offers a serene escape with easy access to business, shopping, and entertainment districts. Whether you’re here for a business trip or a leisurely getaway, our spacious rooms, modern amenities, and attentive service create an unforgettable experience.
        <p>At Sesadul Hotel, we believe that true hospitality lies in the details. Enjoy elegantly designed rooms with stunning views, high-speed Wi-Fi, gourmet dining options, and exclusive facilities such as a rejuvenating spa, state-of-the-art fitness center, and inviting swimming pool. Our professional staff is committed to delivering personalized service, ensuring every guest feels valued and at home.</p>
        <p>Discover why Sesadul Hotel is the preferred choice for travelers who seek comfort, sophistication, and warm hospitality. Whether hosting corporate events, celebrating special moments, or simply unwinding, Sesadul Hotel provides the perfect setting for every occasion.</p>
        <p>STAY IN COMFORT, STAY WITH US.</p>
        </p>
      </div>
    </div>
  </div>

<!-- Our Services Section -->
<section class="our-services" id="service" style="padding: 60px 20px; background: #f8f8f8;">
  <h2 style="text-align: center; color: #008080; font-size: 36px; margin-bottom: 40px;">Our Services</h2>

  <style>
    .services-grid {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 30px;
    }

    .service-box {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      width: 280px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.6s ease forwards;
    }

    .service-box:hover {
      transform: translateY(-10px) scale(1.03);
      box-shadow: 0 12px 25px rgba(0, 128, 128, 0.2);
      background: linear-gradient(135deg, #f0fdfa, #e6f9f9);
    }

    .service-box:nth-child(1) { animation-delay: 0.2s; }
    .service-box:nth-child(2) { animation-delay: 0.4s; }
    .service-box:nth-child(3) { animation-delay: 0.6s; }

    .service-box .icon {
      font-size: 40px;
      color: #008080;
      margin-bottom: 15px;
    }

    .service-box h3 {
      font-size: 20px;
      color: #008080;
      margin-bottom: 10px;
    }

    .service-box p {
      font-size: 14px;
      color: #444;
      margin-bottom: 15px;
    }

    .service-box ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .service-box ul li {
      font-size: 14px;
      color: #333;
      margin: 5px 0;
    }

    .service-box ul li i {
      color: green;
      margin-right: 8px;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

  <div class="services-grid">
    <!-- Service 1 -->
      <div class="service-box">
        <div class="icon">
          <i class="fas fa-dumbbell"></i>
        </div>
        <h3>Fitness Center</h3>
        <p>Stay active during your stay with our fully equipped fitness center featuring modern workout equipment.</p>
        <ul>
          <li><i class="fas fa-check"></i> Modern gym equipment</li>
          <li><i class="fas fa-check"></i> Personal training available</li>
        </ul>
      </div>

      <!-- Service 2 -->
      <div class="service-box">
        <div class="icon">
          <i class="fas fa-swimmer"></i>
        </div>
        <h3>Swimming Pool</h3>
        <p>Relax and unwind at our beautiful outdoor swimming pool, perfect for both leisure and exercise.</p>
        <ul>
          <li><i class="fas fa-check"></i> Poolside lounge chairs</li>
          <li><i class="fas fa-check"></i> Kids' pool area available</li>
        </ul>
      </div>

      <!-- Service 3 -->
      <div class="service-box">
        <div class="icon">
          <i class="fas fa-table"></i>
        </div>
        <h3>Billiard Room</h3>
        <p>Enjoy a casual game of billiards with friends or family in our comfortable and well-maintained billiard room.</p>
        <ul>
          <li><i class="fas fa-check"></i> Professional billiard tables</li>
          <li><i class="fas fa-check"></i> Cozy lounge area</li>
        </ul>
      </div>
  </div>
</section>


  <!-- Contact Us Section -->
<div class="about-us" id="contact" style="background-color: #f3f3f3; padding: 60px 20px;">
  <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; max-width: 1000px; margin: 0 auto;">

<!-- Contact Us Box -->
<div style="flex: 1; min-width: 300px; max-width: 500px; background-color: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
  <h2>Contact Us</h2>
  <p style="margin-bottom: 20px;">Sign Up For Our News Letters</p>
  <form id="contactForm" method="POST" action="send_complaint.php" style="display: flex; flex-direction: column; gap: 15px;">
    <label>Full Name:</label>
    <input type="text" name="fullname" placeholder="Enter your name" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    
    <label>Phone Number:</label>
    <input type="text" name="phone" placeholder="Enter your phone number" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    
    <label>Email Address:</label>
    <input type="email" name="email" placeholder="Enter your email" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
    
    <label>Message:</label>
    <textarea name="message" placeholder="Your message..." required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

    <button type="submit" name="send_complaint" style="background-color: #008080; color: white; padding: 10px 20px; border-radius: 5px;">Send Now</button>
  </form>
</div>

<!-- Popup -->
<div id="popup" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center;">
  <div style="background: white; padding: 30px 40px; border-radius: 10px; text-align: center;">
    <h3>Sending your message...</h3>
    <p>Please wait a moment.</p>
  </div>
</div>

<script>
  const form = document.getElementById('contactForm');
  const popup = document.getElementById('popup');

  form.addEventListener('submit', function () {
    popup.style.display = 'flex';
  });
</script>



    <!-- Connect With Us Box -->
    <div style="flex: 1; min-width: 300px; max-width: 500px; background-color: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
      <h2>Connect With Us</h2>
      <p><strong>Phone:</strong> +62 832-1231-8493</p>
      <p><strong>Email:</strong> INFO@SESADUL.COM</p>
      <p><strong>Address:</strong> Jl. Terusan Jenderal Sudirman, Cimahi, Jawa Barat, Kota Cimahi, Jawa Barat 40525</p>

      <div style="margin-top: 20px; display: flex; gap: 10px;">
        <a href="https://www.facebook.com/SESADULHOTEL/" target="_blank">
          <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Instagram" width="32"></a>
        <a href="https://www.instagram.com/SESADULHOTEL/"  target="_blank">
          <img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Facebook" width="32"></a>
        <a href="https://www.twitter.com/SESADULHOTEL/"  target="_blank">
          <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" width="32"></a>
      </div>

      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.031009807848!2d107.52563277363029!3d-6.886889093112114!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e4559bd023cd%3A0xb21724a94d165f8!2sUniversitas%20Jenderal%20Achmad%20Yani%20(Unjani)!5e0!3m2!1sid!2sid!4v1749631978226!5m2!1sid!2sid" 
        width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

  </div>
</div>


</body>
</html>

<?php
$conn->close();
?>
