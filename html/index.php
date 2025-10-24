<?php
// Simple PHP Landing Page

// You can change this to your site's name
$site_title = "My PHP Site";
$tagline = "The best PHP site";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
  <!-- Include Tailwind CSS -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <!-- DaisyUI -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-base-200 flex items-center justify-center min-h-screen">
  <div class="card w-96 bg-base-100 shadow-xl">
    <div class="card-body">
      <h2 class="card-title text-center">Tailwind + DaisyUI</h2>
      <p class="text-sm text-base-content/70">
        This page uses TailwindCSS and DaisyUI directly from a CDN.
      </p>
      <div class="card-actions justify-end mt-4">
        <button class="btn btn-primary">Cool!</button>
      </div>
    </div>
  </div>
</body>
</html>

