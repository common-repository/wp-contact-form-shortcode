jQuery(document).ready(function () {
    onchangeCity()
});

function onchangeCity() {
    jQuery('.cfs-country').on('change', function () {
        var cityId = jQuery('option:selected', this).data('id');
        jQuery.ajax({
            url: cfs_data.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                _wp_nonce: cfs_data.nonce,
                cfs_city_id: cityId,
                action: 'cfs_load_district'
            },
            success: function (result) {
                if (result.status === 200) {
                    jQuery('.cfs-district').html(result.data);
                }
                jQuery('.cfs-wards').html("<option data-id='0' value=''>---</option>");
                onchangeDistrict();
            }
        })
    });
}

function onchangeDistrict() {
    jQuery('.cfs-district').on('change', function () {
        var districtId = jQuery('option:selected', this).data('id');
        jQuery.ajax({
            url: cfs_data.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                _wp_nonce: cfs_data.nonce,
                cfs_district_id: districtId,
                action: 'cfs_load_wards'
            },
            success: function (result) {
                if (result.status === 200) {
                    jQuery('.cfs-wards').html(result.data)
                }
            }
        })
    })
}