/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$.extend(customThemeActions, {
    updateContentAfter: function(r) {
        // based on /modules/pkcompare/views/js/scripts.min.js
        $('.bt_compare').prop('disabled', !parseInt($('.total-compare-val').text()));
    },
});
/* since 3.1.8 */
