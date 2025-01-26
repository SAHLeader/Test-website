<?php
function EpornerAPICall($api_url, $params) {
    $url = $api_url . '?' . http_build_query($params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $results = curl_exec($ch);
    curl_close($ch);
    return $results;
}

function getEpornerVideos($page = 1, $query = 'all', $per_page = 18) {
    $api_url = 'https://www.eporner.com/api/v2/video/search/';
    $params = array(
        'query' => $query,
        'page' => $page,
        'per_page' => $per_page
    );
    $response = EpornerAPICall($api_url, $params);
    if ($response) {
        return json_decode($response);
    }
    return false;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$query = isset($_GET['query']) ? $_GET['query'] : 'all';
$fetched_videos = 0;
$max_videos_to_fetch = 18;
$apiResponse = getEpornerVideos($page, $query, $max_videos_to_fetch);

if ($apiResponse === false) {
    echo "<p>Error connecting to API. Please try again later.</p>";
    exit;
}

$videos = $apiResponse->videos;
$total_pages = isset($apiResponse->total_pages) ? $apiResponse->total_pages : 1;

$video_to_play = isset($_GET['video_id']) ? $_GET['video_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Videos</title>
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
            color: #8A2BE2; /* Purple */
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        /* Navbar Styles */
        .navbar {
            background-color: #1c1c1c;
            padding: 15px 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 9999;
        }
        .navbar a {
            color: #8A2BE2;
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px;
            border-radius: 5px;
        }
        .navbar a:hover {
            background-color: #333;
            text-decoration: underline;
        }
        .navbar form {
            display: inline-block;
            margin-top: 10px;
        }
        .navbar input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            width: 200px;
        }
        .navbar button {
            padding: 10px 15px;
            background-color: #8A2BE2;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .navbar button:hover {
            background-color: #6A2CE5;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .card {
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
        }
        .card h3 {
            font-size: 18px;
            color: #8A2BE2;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 10px;
        }
        .card a {
            display: block;
            background-color: #8A2BE2;
            color: #fff;
            padding: 12px;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .card a:hover {
            background-color: #6A2CE5;
        }
        .pagination {
            text-align: center;
            padding: 20px;
        }
        .pagination a {
            color: #8A2BE2;
            padding: 10px;
            text-decoration: none;
            margin: 0 5px;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #333;
        }

        /* Media Queries for Mobile */
        @media (max-width: 768px) {
            .navbar input[type="text"] {
                width: 150px;
            }
            .navbar button {
                padding: 8px 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                grid-template-columns: 1fr;
            }
            h1 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="#">Home</a>
    <a href="#">Trending</a>
    <a href="#">Categories</a>
    <a href="#">Favorites</a>
    <form action="" method="GET">
        <input type="text" name="query" placeholder="Search Videos" value="<?php echo htmlspecialchars($query); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<h1>Trending Videos</h1>
<div class="container">
    <?php
    if ($videos && is_array($videos)) {
        foreach ($videos as $video) {
            // Getting the video URL
            $video_url = isset($video->url) ? $video->url : '#';
            $thumbnail = isset($video->default_thumb->src) ? $video->default_thumb->src : 'default-thumbnail.jpg';
            $video_id = isset($video->id) ? $video->id : null;

            echo '<div class="card">';
            echo '<a href="video.php?video_id=' . htmlspecialchars($video_id) . '" class="video-link">
                    <img src="' . htmlspecialchars($thumbnail) . '" alt="Thumbnail">
                  </a>';
            echo '<div class="card-content">';
            echo '<h3>' . htmlspecialchars($video->title) . '</h3>';
            echo '<p>' . htmlspecialchars($video->description ?? 'No description available') . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align: center; width: 100%;">No videos found.</p>';
    }
    ?>
</div>

<div class="pagination">
    <?php
    if ($page > 1) {
        echo '<a href="?page=' . ($page - 1) . '&query=' . urlencode($query) . '">Previous</a>';
    }

    if ($page < $total_pages) {
        echo '<a href="?page=' . ($page + 1) . '&query=' . urlencode($query) . '">Next</a>';
    }
    ?>
</div>

</body>
</html>