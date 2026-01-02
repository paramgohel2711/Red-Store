<?php
// This script creates a placeholder image if it doesn't exist
$placeholderPath = 'assets/images/placeholder.jpg';

if (!file_exists($placeholderPath)) {
    // Create a blank image
    $image = imagecreatetruecolor(300, 300);
    
    // Set background color to light gray
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $textColor = imagecolorallocate($image, 100, 100, 100);
    $text = "No Image";
    
    // Calculate position for centered text
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (300 - $textWidth) / 2;
    $y = (300 - $textHeight) / 2;
    
    // Add the text to the image
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save the image
    imagejpeg($image, $placeholderPath);
    
    // Free memory
    imagedestroy($image);
    
    echo "Placeholder image created successfully!";
} else {
    echo "Placeholder image already exists.";
}
?>

