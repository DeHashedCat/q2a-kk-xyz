<?php

/*
	Question2Answer (c) Gideon Greenspan

	https://www.question2answer.org/

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: https://www.question2answer.org/license.php
*/

class KK_XYZ_Page
{
    private $directory;

    function load_module($directory, $urltoroot)
    {
        $this->directory = $directory;
    }

    public function match_request($request)
    {
        return $request === 'kk_xyz_page';
    }

    public function process_request($request)
    {
        $angle = (int)qa_opt('antibotcaptcha_angle');
        $count = qa_opt('antibotcaptcha_count');
        $font_size_min = 20; // minimum symobl height
        $font_size_max = 32; // maximum symobl height
        $font_file = $this->directory . 'fonts/SpecialElite-Regular.ttf';
        $char_angle_min = -$angle; // maximum skew of the symbol to the left*/
        $char_angle_max = $angle; // maximum skew of the symbol to the right
        $char_angle_shadow = 5; // shadow size
        $char_align = 40; // align symbol verticaly
        $start = 5; // first symbol position
        $interval = (int)qa_opt('antibotcaptcha_interval'); // interval between the start position of characters
        $noise = (int)qa_opt('antibotcaptcha_noise'); // noise level (0 or positive integer)
        $chars = qa_opt('antibotcaptcha_charset'); // charset
        $width = ($count + 1) * $interval; // image width
        $height = 48; // image height

        $image = imagecreatetruecolor($width, $height);

        $background_color = imagecolorallocate($image, 255, 255, 255); // rbg background color
        $font_color = imagecolorallocate($image, 0, 0, 0); // rbg shadow color

        imagefill($image, 0, 0, $background_color);
        imagecolortransparent($image, $background_color);


        $num_chars = strlen($chars);
        $max_index = $num_chars - 1;

        $captcha_chars = [];

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[random_int(0, $max_index)];
            $font_size = random_int($font_size_min, $font_size_max);
            $char_angle = random_int($char_angle_min, $char_angle_max);
            imagettftext($image, $font_size, $char_angle, $start, $char_align, $font_color, $font_file, $char);
            imagettftext($image, $font_size, $char_angle + $char_angle_shadow * (random_int(0, 1) * 2 - 1), $start, $char_align, $background_color, $font_file, $char);
            $start += $interval;
            $captcha_chars[] = $char;
        }
        $_SESSION['IMAGE_CODE'] = implode('', $captcha_chars);
        $this->applyNoise($noise, $width, $height, $image);
        $this->outputImage($image);
    }

    /**
     * @param $image
     *
     * @return void
     */
    private function outputImage($image)
    {
        if (function_exists('imagepng')) {
            header('Content-type: image/png');
            imagepng($image);
        } else if (function_exists('imagegif')) {
            header('Content-type: image/gif');
            imagegif($image);
        } else if (function_exists('imagejpeg')) {
            header('Content-type: image/jpeg');
            imagejpeg($image);
        }

        imagedestroy($image);
    }

    /**
     * @param int $noise
     * @param $width
     * @param int $height
     * @param $image
     *
     * @return void
     */
    private function applyNoise(int $noise, int $width, int $height, $image)
    {
        if ($noise <= 0) {
            return;
        }

        if ($noise <= 70) {
            $num_colors = max(3, min(8, (int)($width / 40)));
            $colors = [];
            for ($i = 0; $i < $num_colors; $i++) {
                $colors[] = imagecolorallocate(
                    $image,
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                );
            }
            $max_width = $width - 1;
            $max_height = $height - 1;

            $color_count = $num_colors - 1;

            $num_lines = (int)($noise / 2.5);


            for ($i = 0; $i < $num_lines; $i++) {
                $x1 = random_int(0, $max_width);
                $y1 = random_int(0, $max_height);

                $x2 = $x1 + random_int(-4, 4);
                $y2 = $y1 + random_int(-4, 4);

                $line_color = $colors[random_int(0, $color_count)];

                imageline($image, $x1, $y1, $x2, $y2, $line_color);
            }


            imagefilter($image, IMG_FILTER_CONTRAST, -(int)($noise / 4));
        }
        else {
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            imagefilter($image, IMG_FILTER_CONTRAST, -12);
            imagefilter($image, IMG_FILTER_SMOOTH, 3);
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }
    }
}