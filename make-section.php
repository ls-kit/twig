<?php
$section = $argv[1] ?? null;

if (!$section) {
    echo "❌ Please provide section name: php make-section hero\n";
    exit;
}

// Create twig file
$twigFile = "templates/partials/{$section}.twig";
$jsonFile = "data/{$section}.json";

if (!file_exists($twigFile)) {
    file_put_contents($twigFile, "<section>\n  <h2>{{ {$section}_title }}</h2>\n  <p>{{ {$section}_desc }}</p>\n</section>");
    echo "✔ Created: $twigFile\n";
}

if (!file_exists($jsonFile)) {
    $data = [
        "{$section}_title" => "Demo Title for {$section}",
        "{$section}_desc" => "This is a sample description for {$section} section."
    ];
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    echo "✔ Created: $jsonFile\n";
}
