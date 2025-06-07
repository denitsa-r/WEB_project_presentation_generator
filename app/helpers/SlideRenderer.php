<?php

class SlideRenderer
{
    public static function render($slide)
    {
        $content = json_decode($slide['content'], true);
        if (!is_array($content)) {
            $content = [$content];
        }

        $layout = $slide['layout'] ?? 'full';
        $title = $slide['title'] ?? '';

        $html = '<div class="slide">';
        $html .= '<h2>' . htmlspecialchars($title) . '</h2>';
        $html .= '<div class="layout-' . htmlspecialchars($layout) . '">';
        $html .= self::getLayoutHTML($layout, $content);
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private static function getLayoutHTML($layout, $elements) {
        switch ($layout) {
            case 'full':
                return self::getContentHTML($elements[0] ?? null);
            
            case '2columns':
                return '
                    <div class="two-columns">
                        <div class="column">' . self::getContentHTML($elements[0] ?? null) . '</div>
                        <div class="column">' . self::getContentHTML($elements[1] ?? null) . '</div>
                    </div>
                ';
            
            case '2rows':
                return '
                    <div class="two-rows">
                        <div class="row">' . self::getContentHTML($elements[0] ?? null) . '</div>
                        <div class="row">' . self::getContentHTML($elements[1] ?? null) . '</div>
                    </div>
                ';
            
            case '3columns':
                return '
                    <div class="three-columns">
                        <div class="column">' . self::getContentHTML($elements[0] ?? null) . '</div>
                        <div class="column">' . self::getContentHTML($elements[1] ?? null) . '</div>
                        <div class="column">' . self::getContentHTML($elements[2] ?? null) . '</div>
                    </div>
                ';
            
            case '3rows':
                return '
                    <div class="three-rows">
                        <div class="row">' . self::getContentHTML($elements[0] ?? null) . '</div>
                        <div class="row">' . self::getContentHTML($elements[1] ?? null) . '</div>
                        <div class="row">' . self::getContentHTML($elements[2] ?? null) . '</div>
                    </div>
                ';
            
            case 'grid2x2':
                return '
                    <div class="grid-2x2">
                        ' . implode('', array_map(function($element) {
                            return '<div class="cell">' . self::getContentHTML($element) . '</div>';
                        }, array_slice($elements, 0, 4))) . '
                    </div>
                ';
            
            case 'grid3x3':
                return '
                    <div class="grid-3x3">
                        ' . implode('', array_map(function($element) {
                            return '<div class="cell">' . self::getContentHTML($element) . '</div>';
                        }, array_slice($elements, 0, 9))) . '
                    </div>
                ';
            
            default:
                return self::getContentHTML($elements[0] ?? null);
        }
    }

    private static function getContentHTML($element) {
        if (!$element) return '';

        $type = $element['type'] ?? 'text';
        $title = $element['title'] ?? '';
        $content = $element['content'] ?? '';

        switch ($type) {
            case 'text':
                return self::formatContent($content);
            case 'image':
                $html = '<div class="content-image">';
                if (!empty($title)) {
                    $html .= '<h3>' . htmlspecialchars($title) . '</h3>';
                }
                $html .= '<img src="' . htmlspecialchars($content) . '" alt="' . htmlspecialchars($title) . '" onerror="this.style.display=\'none\'">';
                if (!empty($element['text'])) {
                    $html .= '<div class="image-text">' . self::formatContent($element['text']) . '</div>';
                }
                $html .= '</div>';
                return $html;
            case 'list':
                $items = explode("\n", $content);
                $html = '<ul>';
                foreach ($items as $item) {
                    if (trim($item) !== '') {
                        $html .= '<li>' . htmlspecialchars(trim($item)) . '</li>';
                    }
                }
                $html .= '</ul>';
                return $html;
            case 'quote':
                $html = '<blockquote>';
                $html .= '<p>' . htmlspecialchars($content) . '</p>';
                if (!empty($title)) {
                    $html .= '<cite>— ' . htmlspecialchars($title) . '</cite>';
                }
                $html .= '</blockquote>';
                return $html;
            default:
                return '';
        }
    }

    private static function formatContent($content) {
        return implode('', array_map(function($line) {
            return '<p>' . htmlspecialchars($line) . '</p>';
        }, explode("\n", $content)));
    }

    public static function getAvailableTypes()
    {
        return [
            'text' => 'Текстов слайд',
            'image' => 'Слайд с изображение',
            'list' => 'Слайд със списък',
            'two-column' => 'Слайд с две колони',
            'quote' => 'Слайд с цитат'
        ];
    }

    public static function getTypeDescription($type)
    {
        $descriptions = [
            'text' => 'Обикновен текстов слайд с заглавие и съдържание',
            'image' => 'Слайд с изображение и опционално заглавие',
            'list' => 'Слайд със списък от елементи (всеки на нов ред)',
            'two-column' => 'Слайд с две колони, разделени с |||',
            'quote' => 'Слайд с цитат (съдържанието е цитатът, заглавието е авторът)'
        ];
        
        return $descriptions[$type] ?? '';
    }
} 