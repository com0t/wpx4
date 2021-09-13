function Campaigns() {
}

function AutoResponders() {
}

Campaigns.prototype.load_campaigns = function (selector_id, campaigns) {

    var select = document.getElementById(selector_id);
    var selected = select.getAttribute('data-selected');

    campaigns.forEach(function (campaign) {
        var option = document.createElement('option');

        option.value = campaign.campaignId;
        option.text = campaign.name;

        if (campaign.campaignId === selected) {
            option.setAttribute('selected', 'selected');
        }

        select.appendChild(option);
    });
};

AutoResponders.prototype.load_responders = function (selector_id, responders) {

    var option;
    var select = document.getElementById(selector_id);
    var selected = select.getAttribute('data-selected');

    select.innerHTML = '';
    var jSelect = jQuery('#' + selector_id);

    var selected_campaign = jSelect.parent().parent().parent().find('.campaign-select').val();

    if (responders[selected_campaign] !== undefined) {
        jSelect.parent().parent().parent().find('.add_to_autoresponder_checkbox').attr('disabled', false);

        for (var id in responders[selected_campaign]) {

            var responder = responders[selected_campaign][id];

            option = document.createElement('option');

            option.value = responder.id;
            option.text = 'Day ' + responder.day + ': ' + responder.name;

            if (responder.id === selected) {
                option.setAttribute('selected', 'selected');
            }

            select.appendChild(option);
        }
    } else {
        jSelect.parent().parent().parent().find('.add_to_autoresponder_checkbox').removeAttr('checked');
        jSelect.parent().parent().parent().find('.add_to_autoresponder_checkbox').attr('disabled', true);
        jSelect.attr('disabled', true);
        option = document.createElement('option');
        option.value = null;
        option.text = 'no autoresponders';
        select.appendChild(option);
    }
};
