<?php
// جلب معرف الفيديو من الرابط
$video_id = isset($_GET['video_id']) ? $_GET['video_id'] : null;

if (!$video_id) {
    echo "<p>Video not found.</p>";
    exit;
}

// رابط الفيديو الرئيسي
$video_url = "https://www.eporner.com/embed/" . $video_id . "/";

// للحصول على عنوان الفيديو ومقترحات الفيديوهات (محاكاة API)
$api_url = 'https://www.eporner.com/api/v2/video/search/';
$params = array(
    'query' => 'trending',
    'per_page' => 6,
    'page' => 1
);
$api_full_url = $api_url . '?' . http_build_query($params);

$response = file_get_contents($api_full_url);
$recommended_videos = $response ? json_decode($response)->videos : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #111;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            padding: 40px;
            color: #8A2BE2;
            font-size: 36px;
            font-weight: bold;
        }
        .video-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .video-wrapper {
            position: relative;
            padding-top: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
            background: black;
            border-radius: 10px;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-title {
            text-align: center;
            margin-top: 15px;
            font-size: 24px;
            color: #fff;
        }
        .recommended-section {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .recommended-section h2 {
            color: #8A2BE2;
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .recommended-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 10px 0;
        }
        .card {
            background-color: #222;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 280px;
            flex-shrink: 0;
            text-align: center;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        .card h3 {
            font-size: 18px;
            color: #8A2BE2;
            padding: 10px;
        }
        .card a {
            text-decoration: none;
        }
        .back-button {
            display: block;
            text-align: center;
            background-color: #8A2BE2;
            color: white;
            padding: 10px 20px;
            margin: 30px auto;
            width: 200px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
        }
        .back-button:hover {
            background-color: #6A2CE5;
        }

        /* Media Queries for Mobile */
        @media (max-width: 768px) {
            .video-container {
                padding: 10px;
            }
            .video-wrapper {
                padding-top: 56.25%; /* Adjust aspect ratio for mobile */
            }
            .recommended-container {
                display: flex;
                overflow-x: auto;
                gap: 10px;
            }
            .card {
                width: 230px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 28px;
            }
            .video-title {
                font-size: 20px;
            }
            .recommended-section h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<!-- الفيديو الرئيسي -->
<div class="video-container">
    <div class="video-wrapper">
        <iframe src="<?php echo $video_url; ?>" allowfullscreen></iframe>
    </div>
    <h1 class="video-title">Video Title Placeholder</h1>
</div>

<!-- قسم الفيديوهات المقترحة -->
<div class="recommended-section">
    <h2>Recommended Videos</h2>
    <div class="recommended-container">
        <?php
        if ($recommended_videos) {
            foreach ($recommended_videos as $video) {
                $thumbnail = isset($video->default_thumb->src) ? $video->default_thumb->src : 'default-thumbnail.jpg';
                $title = isset($video->title) ? $video->title : 'No title';
                $video_id = isset($video->id) ? $video->id : null;

                echo '<div class="card">';
                echo '<a href="video.php?video_id=' . htmlspecialchars($video_id) . '">';
                echo '<img src="' . htmlspecialchars($thumbnail) . '" alt="Thumbnail">';
                echo '<h3>' . htmlspecialchars($title) . '</h3>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo '<p style="text-align: center; color: #ccc;">No recommended videos available.</p>';
        }
        ?>
    </div>
</div>

<!-- زر الرجوع -->
<a href="index.php" class="back-button">Back to Home</a>

</body>
</html>