<?php
function getMetaTags($url) {
    $tags = get_meta_tags($url);
    return [
        'title' => $tags['title'] ?? 'N/A',
        'description' => $tags['description'] ?? 'N/A'
    ];
}

function checkMobileFriendly($url) {
    return (strpos(file_get_contents($url), 'viewport') !== false) ? 'Yes' : 'No';
}

function checkBrokenLinks($url) {
    $html = file_get_contents($url);
    preg_match_all('/<a href=["\'](http[^"\']+)["\']/', $html, $links);
    
    $brokenLinks = [];
    foreach ($links[1] as $link) {
        $headers = @get_headers($link);
        if (!$headers || strpos($headers[0], '404') !== false) {
            $brokenLinks[] = $link;
        }
    }
    return $brokenLinks;
}

if (isset($_POST['url'])) {
    $url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
    $meta = getMetaTags($url);
    $mobileFriendly = checkMobileFriendly($url);
    $brokenLinks = checkBrokenLinks($url);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SEO Analyzer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        input, button {
            padding: 10px;
            margin: 10px 0;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SEO Analyzer Tool</h1>
        <p>Enter a website URL to analyze its SEO performance, including metadata, mobile-friendliness, and broken links.</p>
        <form method="POST">
            <input type="text" name="url" placeholder="Enter Website URL" required>
            <button type="submit">Analyze</button>
        </form>
        
        <?php if (isset($url)): ?>
            <h2>SEO Analysis for: <?php echo htmlspecialchars($url); ?></h2>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($meta['title']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($meta['description']); ?></p>
            <p><strong>Mobile Friendly:</strong> <?php echo $mobileFriendly; ?></p>
            <p><strong>Broken Links:</strong> <?php echo empty($brokenLinks) ? 'None' : implode(', ', $brokenLinks); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
