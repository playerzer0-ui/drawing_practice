<?php
require_once "../vendor/autoload.php";

use Codedge\Fpdf\Fpdf\Fpdf;

class ProgressPDF extends Fpdf
{
    private $imageMargin = 4; // Space between images

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'User Progress Report', 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Year: ' . date('Y'), 0, 1, 'L');

        $this->Ln(4);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    /* ---------- SUMMARY ---------- */

    function MonthHeader(string $title)
    {
        // Check if we need a new page
        if ($this->GetY() > 250) {
            $this->AddPage();
        }

        $this->Ln(6);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, $title, 0, 1);
        $this->Ln(2);
    }

    function BarRow(string $label, int $value, int $max)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(45, 7, $label);

        $barWidth = 100;
        $filled = ($value / max(1, $max)) * $barWidth;

        $x = $this->GetX();
        $y = $this->GetY();

        $this->Rect($x, $y + 2, $barWidth, 4);
        if ($value > 0) {
            $this->SetFillColor(60, 60, 60);
            $this->Rect($x, $y + 2, $filled, 4, 'F');
        }

        $this->SetX($x + $barWidth + 5);
        $this->Cell(10, 7, $value, 0, 1);
    }

    /* ---------- GALLERY ---------- */

    function GalleryHeader(string $month)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, $month . ' - Gallery', 0, 1);
        $this->Ln(4);
    }

    /**
     * Display two images side by side, maintaining aspect ratio
     * 
     * @param string|null $original Original image path or null for prompt-based
     * @param string $drawn Drawn image path
     * @param string $description Description or prompt for the task
     * @param string $label Label for the image pair
     * @param float $maxWidth Maximum width for each image (default 80)
     * @param float $maxHeight Maximum height for each image (default 80)
     */
    function ImagePair(?string $original, string $drawn, string $description, string $label, float $maxWidth = 80, float $maxHeight = 80)
    {
        // Check if we need a new page (leave space for label)
        if ($this->GetY() + $maxHeight + 15 > $this->PageBreakTrigger) {
            $this->AddPage();
        }

        $yStart = $this->GetY();
        $xPos1 = 10; // Left image position
        $xPos2 = $xPos1 + $maxWidth + $this->imageMargin; // Right image position

        // Draw left image (original or description)
        if ($original) {
            try {
                // Get image dimensions first
                $originalInfo = @getimagesize($original);
                if ($originalInfo) {
                    list($imgWidth, $imgHeight) = $originalInfo;

                    // Calculate dimensions to fit within max bounds while maintaining aspect ratio
                    $widthRatio = $maxWidth / $imgWidth;
                    $heightRatio = $maxHeight / $imgHeight;
                    $ratio = min($widthRatio, $heightRatio);

                    $finalWidth = $imgWidth * $ratio;
                    $finalHeight = $imgHeight * $ratio;

                    // Draw border
                    $this->Rect($xPos1, $yStart, $maxWidth, $maxHeight);

                    // Center the image in the available space
                    $xOffset = ($maxWidth - $finalWidth) / 2;
                    $yOffset = ($maxHeight - $finalHeight) / 2;

                    // Insert the image
                    $this->Image($original, $xPos1 + $xOffset, $yStart + $yOffset, $finalWidth, $finalHeight);

                    // Add "Original" label
                    $this->SetFont('Arial', 'I', 8);
                    $this->SetXY($xPos1, $yStart + $maxHeight - 5);
                    $this->Cell($maxWidth, 5, 'Original', 0, 0, 'C');
                } else {
                    throw new Exception("Could not get image info");
                }
            } catch (Exception $e) {
                $this->DrawDescription($xPos1, $yStart, $maxWidth, $maxHeight, $description, "Original (URL)");
            }
        } else {
            // No original image, show the description
            $this->DrawDescription($xPos1, $yStart, $maxWidth, $maxHeight, $description, "Prompt Description");
        }

        // Draw right image (drawn) - using existing InsertImage method
        if (file_exists($drawn)) {
            $this->InsertImage($drawn, $xPos2, $yStart, $maxWidth, $maxHeight, "Drawn");
        } else {
            $this->DrawPlaceholder($xPos2, $yStart, $maxWidth, $maxHeight, "Not Available");
        }

        // Update Y position to the bottom of the images (both use maxHeight)
        $imgBottom = $yStart + $maxHeight;
        $this->SetY($imgBottom + 2);

        // Add label
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 6, $label, 0, 1);

        $this->Ln(4);
    }

    /**
     * Draw description text in a box with word wrapping
     */
    private function DrawDescription(float $x, float $y, float $width, float $height, string $description, string $title = "")
    {
        // Draw border
        $this->Rect($x, $y, $width, $height);

        // Add title if provided
        if ($title) {
            $this->SetFont('Arial', 'I', 9);
            $this->SetXY($x, $y + 2);
            $this->Cell($width, 5, $title, 0, 0, 'C');
        }

        // Split description by $$ delimiter
        $parts = explode("$$", $description);
        $lineHeight = 5;
        $startY = $y + ($title ? 8 : 5); // Start lower if there's a title

        $this->SetFont('Arial', 'I', 10);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Calculate how many lines this part will need
            $charWidth = 2.5; // Approximate width of a character in mm for Arial 10
            $maxCharsPerLine = floor($width / $charWidth) - 2; // Leave some margin

            if (strlen($part) <= $maxCharsPerLine) {
                // Single line
                $this->SetXY($x, $startY);
                $this->Cell($width, $lineHeight, $part, 0, 0, 'C');
                $startY += $lineHeight;
            } else {
                // Multi-line - need to split into multiple lines
                $words = explode(' ', $part);
                $currentLine = '';

                foreach ($words as $word) {
                    $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;

                    if (strlen($testLine) <= $maxCharsPerLine) {
                        $currentLine = $testLine;
                    } else {
                        // Output current line
                        if ($currentLine) {
                            $this->SetXY($x, $startY);
                            $this->Cell($width, $lineHeight, $currentLine, 0, 0, 'C');
                            $startY += $lineHeight;
                        }

                        // If a single word is too long, break it
                        if (strlen($word) > $maxCharsPerLine) {
                            $chunks = str_split($word, $maxCharsPerLine);
                            foreach ($chunks as $chunk) {
                                $this->SetXY($x, $startY);
                                $this->Cell($width, $lineHeight, $chunk, 0, 0, 'C');
                                $startY += $lineHeight;
                            }
                            $currentLine = '';
                        } else {
                            $currentLine = $word;
                        }
                    }
                }

                // Output the last line
                if ($currentLine) {
                    $this->SetXY($x, $startY);
                    $this->Cell($width, $lineHeight, $currentLine, 0, 0, 'C');
                    $startY += $lineHeight;
                }
            }

            // Add spacing between parts
            $startY += 2;

            // Check if we're running out of space
            if ($startY + $lineHeight > $y + $height - 2) {
                break;
            }
        }
    }

    /**
     * Insert an image while maintaining aspect ratio
     */
    private function InsertImage(string $imagePath, float $x, float $y, float $maxWidth, float $maxHeight, string $label = "")
    {
        if (!file_exists($imagePath)) {
            $this->DrawPlaceholder($x, $y, $maxWidth, $maxHeight, "File not found");
            return;
        }

        // Get image dimensions
        $imageInfo = @getimagesize($imagePath);
        if (!$imageInfo) {
            $this->DrawPlaceholder($x, $y, $maxWidth, $maxHeight, "Invalid image");
            return;
        }

        list($imgWidth, $imgHeight) = $imageInfo;

        // Calculate dimensions to fit within max bounds while maintaining aspect ratio
        $widthRatio = $maxWidth / $imgWidth;
        $heightRatio = $maxHeight / $imgHeight;
        $ratio = min($widthRatio, $heightRatio);

        $finalWidth = $imgWidth * $ratio;
        $finalHeight = $imgHeight * $ratio;

        // Center the image in the available space
        $xOffset = ($maxWidth - $finalWidth) / 2;
        $yOffset = ($maxHeight - $finalHeight) / 2;

        // Draw border
        $this->Rect($x, $y, $maxWidth, $maxHeight);

        // Insert the image
        $this->Image($imagePath, $x + $xOffset, $y + $yOffset, $finalWidth, $finalHeight);

        // Add label below image (optional)
        if ($label) {
            $this->SetFont('Arial', 'I', 8);
            $this->SetXY($x, $y + $maxHeight - 5);
            $this->Cell($maxWidth, 5, $label, 0, 0, 'C');
        }
    }

    /**
     * Draw a placeholder box when image is not available
     */
    private function DrawPlaceholder(float $x, float $y, float $width, float $height, string $text)
    {
        // Draw border
        $this->Rect($x, $y, $width, $height);

        // Draw text in the center
        $this->SetFont('Arial', 'I', 9);
        $this->SetXY($x, $y + ($height / 2) - 3);

        // Split long text into multiple lines
        $textWidth = $this->GetStringWidth($text);
        if ($textWidth > $width - 4) {
            // Estimate characters per line
            $charsPerLine = floor(strlen($text) * ($width - 4) / $textWidth);
            $lines = str_split($text, max(10, $charsPerLine));

            $lineHeight = 4;
            $startY = $y + ($height / 2) - (count($lines) * $lineHeight / 2);

            foreach ($lines as $i => $line) {
                $this->SetXY($x, $startY + ($i * $lineHeight));
                $this->Cell($width, $lineHeight, trim($line), 0, 0, 'C');
            }
        } else {
            $this->Cell($width, 6, $text, 0, 0, 'C');
        }
    }
}
