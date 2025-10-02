<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HOME PAGE | EduAxis</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="icon" type="image/x-icon" href="IMGS/LOGO.ico">
  <link rel="stylesheet" href="CSS/main.css" />
</head>
<body>
  <div class="img">
    <img src="IMGS/LOGO.jpg" alt="EduAxis Logo" />
  </div>

  <h1 class="text-center"><b>WELCOME TO EDUAXIS STUDY CENTRE</b></h1>
  <hr/>

  <ul>
    <li style="float: right;"><a class="active" href="LOGIN_FORM.php" id="loginBtn" target="_blank">LOGIN</a></li>
    <li style="float: right;"><a class="active" href="REGISTER_FORM.php" target="_blank">REGISTER</a></li>
  </ul>
  <hr />

  <div class="hero-slider">
    <div class="hero1">
      <i class="fas fa-book" style="font-size:48px;"></i>
      <h1><b>ENJOY YOUR STAY</b></h1>
      <h1><b>AND STUDY</b></h1>
      <h1><b>WITH EDUAXIS</b></h1>
    </div>
    <div class="hero2">
      <i class="fas fa-clock" style="font-size:48px;"></i>
      <h1><b>CHOOSE YOUR PERFECT STUDY TIME:</b></h1>
      <h1><b>MORNING, AFTERNOON OR EVENING</b></h1>
    </div>
    <div class="hero3">
      <i class="fas fa-money-bill" style="font-size:48px;"></i>
      <h1><b>PERSONAL STUDY ZONE</b></h1>
      <h1><b>AT AN</b></h1>
      <h1><b>AFFORDABLE PRICE</b></h1>
    </div>
  </div>

  <section class="features-section text-center py-5">
    <div id="loginContainer" class="container mt-4"></div>
  </section>

  <section class="features-section text-center py-5">
    <hr class="mb-4 new1" />
    <h2><u><b>OUR FACILITIES</b></u></h2>
    <div class="row mt-4 justify-content-center">
      <div class="col-md-4">
        <i class="fa fa-book" style="font-size: 40px;"></i>  
        <i class="fa fa-volume-mute" style="font-size: 40px;"></i>  
        <i class="fa fa-user-graduate" style="font-size: 40px;"></i>  
        <h3><b>QUIET</b></h3>
        <h3><b>STUDY</b></h3>
        <h3><b>ROOMS</b></h3>
      </div>
      <div class="col-md-4">
        <i class="fa fa-chair" style="font-size: 40px;"></i>
        <h3><b>COMFORTABLE</b></h3>
        <h3><b>SEATS</b></h3>
      </div>
      <div class="col-md-4">
        <i class="fa fa-wifi" style="font-size: 40px;"></i>
        <h3><b>FREE</b></h3>
        <h3><b>WIFI</b></h3>
      </div>
    </div>
  </section>

  <section class="features-section text-center py-5">
    <hr class="mb-4 new1" />
    <br />
    <h2><u><b>WHY EDUAXIS?</b></u></h2>
    <br />
    <div class="row mt-4 justify-content-center">
      <div class="col-md-4">
        <i class="fa fa-shield-alt" style="font-size: 40px;"></i>
        <i class="fa fa-broom" style="font-size: 40px;"></i>
        <h3><b>SAFE AND CLEAN</b></h3>
        <h3><b>ENVIRONMENT</b></h3>
      </div>
      <div class="col-md-4">
        <i class="fa-solid fa-person" style="font-size: 40px;"></i>
        <h3><b>FRIENDLY</b></h3>
        <h3><b>STAFF</b></h3>
      </div>
      <div class="col-md-4">
        <i class="fa fa-chair" style="font-size: 40px;"></i>
        <h3><b>LIMITED SEATS</b></h3>
        <h3><b>PER</b></h3>
        <h3><b>SESSION</b></h3>
      </div>
    </div>
  </section>

  <hr />
<h3 style="text-align: center; font-weight: bold;"><u>SLOT INFORMATION AND PRICING</u></h3>

<div class="table-wrap">
  <table class="center">
    <tr>
      <th>SLOTS</th>
      <th style="text-align: center;">TIMINGS</th>
      <th>PRICING</th>
    </tr>
    <tr>
      <td>SLOT A</td>
      <td>08:00 AM - 12:00 PM</td>
      <td>Rs. 500</td>
    </tr>
    <tr>
      <td>SLOT B</td>
      <td>01:00 PM - 05:00 PM</td>
      <td>Rs. 500</td>
    </tr>
    <tr>
      <td>SLOT C</td>
      <td>06:00 PM - 10:00 PM</td>
      <td>Rs. 500</td>
    </tr>
  </table>
</div>

  <hr />

  <h3 style="text-align: center;"><b><u>CONTACT FORM AND LOCATION</u></b></h3>

  <div class="container contact-section">
    <div class="row">
      <!-- Contact Form -->
      <div class="col-md-6">
        <form id="contactForm">
  <label for="fname">Full Name</label>
  <input type="text" id="fname" name="fullname" placeholder="Your Full Name.." required />

  <label for="lname">Contact Number</label>
  <input type="text" id="lname" name="contact_number" placeholder="Your Contact Number.." required />

  <label for="eid"> Email ID</label>
  <input type="text" id="eid" name="email_id" placeholder="Your Email ID..." required/>


  <label for="subject">Suggestions & Queries </label>
  <textarea id="subject" name="subject" placeholder="Write something.." style="height:200px" required></textarea>

  <input type="submit" value="Submit" class="btn btn-primary" />
</form>

<div id="formMessage" class="mt-3"></div>
</div>
      <div class="col-md-6">
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d188699.90483284293!2d77.38518910710988!3d28.50058781226699!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cea64b8f89aef%3A0xec0ccabb5317962e!2sGreater%20Noida%2C%20Uttar%20Pradesh%2C%20India!5e0!3m2!1sen!2sae!4v1754665273170!5m2!1sen!2sae"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>

        <div class="contact-info">
          <h5>CONTACT</h5>
          <p><strong>Location:</strong> Greater Noida, India</p>
          <p><strong>Email ID:</strong> <a href="mailto:contact@eduaxis.com" target="_blank">contact@eduaxis.com</a></p>
          <p><strong>Phone:</strong> +91 8707681810</p>
        </div>
      </div>
    </div>
  </div>

  <div id="php-content" style="margin: 20px auto; max-width: 400px;"></div>

  <footer class="text-center py-4 bg-dark text-light">
    <p>&copy; 2025 EduAxis. All rights reserved.</p>
  </footer>

  <script>

      document.getElementById("contactForm").addEventListener("submit", function (e) {
    e.preventDefault(); // prevent page reload

    const form = this;
    const formData = new FormData(form);

    fetch("INCLUDES/contact.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        const messageBox = document.getElementById("formMessage");
        if (data.status === "success") {
          messageBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
          form.reset();
        } else {
          messageBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
        setTimeout(() => {
          messageBox.innerHTML = "";
        }, 4000);
      })
      .catch((error) => {
        console.error("Fetch error:", error);
        document.getElementById("formMessage").innerHTML =
          `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
      });
  });
</script>
</body>
</html>
