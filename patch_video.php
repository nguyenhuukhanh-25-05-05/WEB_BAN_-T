<?php
$file = 'index.php';
$content = file_get_contents($file);

$search = '<img src="https://images.unsplash.com/photo-1616348436168-de43ad0db179?auto=format&fit=crop&q=80&w=1000" class="img-fluid rounded-5" alt="Store">';
$search2 = '<div class="play-button-glass"><i class="bi bi-play-fill"></i></div>';

$replace = '<video src="https://cdn.pixabay.com/video/2020/05/26/40149-425265495_large.mp4" class="img-fluid rounded-5 w-100" style="object-fit: cover; aspect-ratio: 1/1;" autoplay muted loop playsinline></video>';

$content = str_replace($search, $replace, $content);
$content = str_replace($search2, '', $content);

file_put_contents($file, $content);
echo "Done";
?>
