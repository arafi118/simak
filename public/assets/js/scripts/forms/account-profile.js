function SetSwitchery() {
    var elems = $('.switchery');
    $.each(elems, function (key, value) {
        if (this.switchery) {
            $(this).next('.switchery').remove();
            delete this.switchery;
        }

        var $size = "",
            $color = "",
            $sizeClass = "",
            $colorCode = "";
        $size = $(this).data('size');
        var $sizes = {
            'lg': "large",
            'sm': "small",
            'xs': "xsmall"
        };
        if ($(this).data('size') !== undefined) {
            $sizeClass = "switchery switchery-" + $sizes[$size];
        } else {
            $sizeClass = "switchery";
        }

        $color = $(this).data('color');
        var $colors = {
            'primary': "#967ADC",
            'success': "#37BC9B",
            'danger': "#DA4453",
            'warning': "#F6BB42",
            'info': "#3BAFDA"
        };
        if ($color !== undefined) {
            $colorCode = $colors[$color];
        } else {
            $colorCode = "#37BC9B";
        }

        this.switchery = new Switchery($(this)[0], {
            className: $sizeClass,
            color: $colorCode
        });
    });
}

SetSwitchery()
