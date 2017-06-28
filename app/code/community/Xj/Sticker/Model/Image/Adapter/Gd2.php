<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Xj
 * @package     Xj_Sticker
 * @copyright   Copyright (c) 2012 Xj
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Image GD2 Adapter
 *
 * @author Xj
 */
class Xj_Sticker_Model_Image_Adapter_Gd2
extends Varien_Image_Adapter_Gd2
{
    private static $_callbacks = array(
        IMAGETYPE_GIF  => array('output' => 'imagegif',  'create' => 'imagecreatefromgif'),
        IMAGETYPE_JPEG => array('output' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'),
        IMAGETYPE_PNG  => array('output' => 'imagepng',  'create' => 'imagecreatefrompng'),
        IMAGETYPE_XBM  => array('output' => 'imagexbm',  'create' => 'imagecreatefromxbm'),
        IMAGETYPE_WBMP => array('output' => 'imagewbmp', 'create' => 'imagecreatefromxbm'),
    );

    /**
     * Obtain function name, basing on image type and callback type
     *
     * @param string $callbackType
     * @param int $fileType
     * @return string
     * @throws Exception
     */
    private function _getCallback($callbackType, $fileType = null, $unsupportedText = 'Unsupported image format.')
    {
        if (null === $fileType) {
            $fileType = $this->_fileType;
        }
        if (empty(self::$_callbacks[$fileType])) {
            throw new Exception($unsupportedText);
        }
        if (empty(self::$_callbacks[$fileType][$callbackType])) {
            throw new Exception('Callback not found.');
        }
        return self::$_callbacks[$fileType][$callbackType];
    }

    private function _fillBackgroundColor(&$imageResourceTo)
    {
        // try to keep transparency, if any
        if ($this->_keepTransparency) {
            $isAlpha = false;
            $transparentIndex = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
            try {
                // fill truecolor png with alpha transparency
                if ($isAlpha) {

                    if (!imagealphablending($imageResourceTo, false)) {
                        throw new Exception('Failed to set alpha blending for PNG image.');
                    }
                    $transparentAlphaColor = imagecolorallocatealpha($imageResourceTo, 0, 0, 0, 127);
                    if (false === $transparentAlphaColor) {
                        throw new Exception('Failed to allocate alpha transparency for PNG image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentAlphaColor)) {
                        throw new Exception('Failed to fill PNG image with alpha transparency.');
                    }
                    if (!imagesavealpha($imageResourceTo, true)) {
                        throw new Exception('Failed to save alpha transparency into PNG image.');
                    }

                    return $transparentAlphaColor;
                }
                // fill image with indexed non-alpha transparency
                elseif (false !== $transparentIndex) {
                    $transparentColor = false;
                    if ($transparentIndex >=0 && $transparentIndex <= imagecolorstotal($this->_imageHandler)) {
                        list($r, $g, $b)  = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
                        $transparentColor = imagecolorallocate($imageResourceTo, $r, $g, $b);
                    }
                    if (false === $transparentColor) {
                        throw new Exception('Failed to allocate transparent color for image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentColor)) {
                        throw new Exception('Failed to fill image with transparency.');
                    }
                    imagecolortransparent($imageResourceTo, $transparentColor);
                    return $transparentColor;
                }
            }
            catch (Exception $e) {
                // fallback to default background color
            }
        }
        list($r, $g, $b) = $this->_backgroundColor;
        $color = imagecolorallocate($imageResourceTo, $r, $g, $b);
        if (!imagefill($imageResourceTo, 0, 0, $color)) {
            throw new Exception("Failed to fill image background with color {$r} {$g} {$b}.");
        }

        return $color;
    }

    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false)
    {
        $isAlpha     = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png only
        if ((IMAGETYPE_GIF === $fileType) || (IMAGETYPE_PNG === $fileType)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            }
            // assume that truecolor PNG has transparency
            elseif (IMAGETYPE_PNG === $fileType) {
                $isAlpha     = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex; // -1
            }
        }
        if (IMAGETYPE_JPEG === $fileType) {
            $isTrueColor = true;
        }
        return false;
    }

    public function stickerWatermark($watermarkImage, $positionX=0, $positionY=0, /* $watermarkImageOpacity=30, */ $repeat=false)
    {
        list($watermarkSrcWidth, $watermarkSrcHeight, $watermarkFileType, ) = getimagesize($watermarkImage);
        $this->_getFileAttributes();
        $watermark = call_user_func($this->_getCallback(
            'create',
            $watermarkFileType,
            'Unsupported watermark image format.'
        ), $watermarkImage);

        $merged = false;
        if ($this->getWatermarkWidth() &&
            $this->getWatermarkHeigth() &&
            ($this->getWatermarkPosition() != self::POSITION_STRETCH)
        ) {
            $newWatermark = imagecreatetruecolor($this->getWatermarkWidth(), $this->getWatermarkHeigth());
            imagealphablending($newWatermark, false);
            $col = imagecolorallocatealpha($newWatermark, 255, 255, 255, 127);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->getWatermarkWidth(), $this->getWatermarkHeigth(), $col);
            imagealphablending($newWatermark, true);
            imageSaveAlpha($newWatermark, true);
            imagecopyresampled(
                $newWatermark,
                $watermark,
                0, 0, 0, 0,
                $this->getWatermarkWidth(), $this->getWatermarkHeigth(),
                imagesx($watermark), imagesy($watermark)
            );
            $watermark = $newWatermark;
        }

        if( $this->getWatermarkPosition() == self::POSITION_TILE ) {
            $repeat = true;
        } elseif( $this->getWatermarkPosition() == self::POSITION_STRETCH ) {
            $newWatermark = imagecreatetruecolor($this->_imageSrcWidth, $this->_imageSrcHeight);
            imagealphablending($newWatermark, false);
            $col = imagecolorallocatealpha($newWatermark, 255, 255, 255, 127);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, $col);
            imagealphablending($newWatermark, true);
            imageSaveAlpha($newWatermark, true);
            imagecopyresampled(
                $newWatermark,
                $watermark,
                0, 0, 0, 0,
                $this->_imageSrcWidth, $this->_imageSrcHeight,
                imagesx($watermark), imagesy($watermark)
            );
            $watermark = $newWatermark;

        } else

            if( $this->getWatermarkPosition() == self::POSITION_CENTER ) {
                $positionX = ($this->_imageSrcWidth/2 - imagesx($watermark)/2);
                $positionY = ($this->_imageSrcHeight/2 - imagesy($watermark)/2);
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_TOP_RIGHT ) {
                $positionX = ($this->_imageSrcWidth - imagesx($watermark));
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_TOP_LEFT  ) {
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_BOTTOM_RIGHT ) {
                $positionX = ($this->_imageSrcWidth - imagesx($watermark));
                $positionY = ($this->_imageSrcHeight - imagesy($watermark));
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_BOTTOM_LEFT ) {
                $positionY = ($this->_imageSrcHeight - imagesy($watermark));
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_BOTTOM_CENTER ) {
                $positionX = ($this->_imageSrcWidth/2 - imagesx($watermark)/2);
				$positionY = ($this->_imageSrcHeight - imagesy($watermark));
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_TOP_CENTER ) {
                $positionX = ($this->_imageSrcWidth/2 - imagesx($watermark)/2);
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_LEFT_CENTER ) {
                $positionY = ($this->_imageSrcHeight/2 - imagesy($watermark)/2);
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            } elseif( $this->getWatermarkPosition() == self::POSITION_RIGHT_CENTER ) {
                $positionX = ($this->_imageSrcWidth - imagesx($watermark));
				$positionY = ($this->_imageSrcHeight/2 - imagesy($watermark)/2);
                imagecopy(
                    $this->_imageHandler,
                    $watermark,
                    $positionX, $positionY,
                    0, 0,
                    imagesx($watermark), imagesy($watermark)
                );
            }


        if( $repeat === false && $merged === false ) {
            imagecopy(
                $this->_imageHandler,
                $watermark,
                $positionX, $positionY,
                0, 0,
                imagesx($watermark), imagesy($watermark)
            );
        } else {
            $offsetX = $positionX;
            $offsetY = $positionY;
            while( $offsetY <= ($this->_imageSrcHeight+imagesy($watermark)) ) {
                while( $offsetX <= ($this->_imageSrcWidth+imagesx($watermark)) ) {
                    imagecopy(
                        $this->_imageHandler,
                        $watermark,
                        $offsetX, $offsetY,
                        0, 0,
                        imagesx($watermark), imagesy($watermark)
                    );
                    $offsetX += imagesx($watermark);
                }
                $offsetX = $positionX;
                $offsetY += imagesy($watermark);
            }
        }

        imagedestroy($watermark);
        $this->refreshImageDimensions();
    }

    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }

    /*
     * Fixes saving PNG alpha channel
     */
    private function _saveAlpha($imageHandler)
    {
        $background = imagecolorallocate($imageHandler, 0, 0, 0);
        ImageColorTransparent($imageHandler, $background);
        imagealphablending($imageHandler, false);
        imagesavealpha($imageHandler, true);
    }
}
