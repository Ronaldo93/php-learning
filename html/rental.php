<?php
// Connect to the database
$mysqli = new mysqli("localhost", "user", "Password123!", "rental");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch rental properties
$query = "SELECT id, property_name, owner, address, phone, image_url FROM rentals WHERE property_name IS NOT NULL AND property_name != ''";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rental Properties</title>
  <!-- Include Tailwind CSS -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <!-- DaisyUI -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
</head>
<body class="bg-base-200 min-h-screen py-8">
  <div class="container mx-auto px-4">
    <!-- Header -->
    <div class="text-center mb-8">
      <h1 class="text-4xl font-bold mb-2">Available Rental Properties</h1>
      <p class="text-base-content/70">Find your perfect home from our selection</p>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              ?>
              <div class="card bg-base-100 shadow-xl">
                <figure class="h-48 bg-base-300">
                  <?php if (!empty($row['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($row['property_name']); ?>"
                         class="w-full h-full object-cover">
                   <?php else: ?>
                    <!-- default later -->
                    <!-- <div class="flex items-center justify-center h-full w-full text-base-content/30">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                      </svg>
                    </div>
                   -->
                  <?php endif; ?>
                  <!-- demo -->
                  <img src="home.jpg" alt="demo home" class="w-full h-full object-cover">

                </figure>
                <div class="card-body">
                  <h2 class="card-title"><?php echo htmlspecialchars($row['property_name']); ?></h2>

                  <div class="space-y-2 text-sm">
                    <div class="flex items-start gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span class="text-base-content/70"><?php echo htmlspecialchars($row['address']); ?></span>
                    </div>

                    <div class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      <span class="text-base-content/70"><?php echo htmlspecialchars($row['owner']); ?></span>
                    </div>

                    <div class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      <span class="text-base-content/70"><?php echo htmlspecialchars($row['phone']); ?></span>
                    </div>
                  </div>

                  <div class="card-actions justify-end mt-4">
                    <button class="btn btn-primary btn-sm">Contact Owner</button>
                  </div>
                </div>
              </div>
              <?php
          }
      } else {
          ?>
          <div class="col-span-full text-center py-12">
            <p class="text-xl text-base-content/70">No rental properties available at the moment.</p>
          </div>
          <?php
      }

      $mysqli->close();
      ?>
    </div>
  </div>
</body>
</html>