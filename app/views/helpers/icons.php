<?

class IconsHelper extends AppHelper {
    function i ($charEntity = '&#xe000;', $before = '', $after = '') {
        return $before.'<span data-icon="'.$charEntity.'" aria-hidden="true"></span>'.$after;
    }
}

?>