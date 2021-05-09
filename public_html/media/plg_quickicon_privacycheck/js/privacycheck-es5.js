(function () {
  'use strict';

  /**
   * @copyright  (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  (function (document) {
    document.addEventListener('DOMContentLoaded', function () {
      var variables = Joomla.getOptions('js-privacy-check');
      var ajaxUrl = variables.plg_quickicon_privacycheck_ajax_url;
      var url = variables.plg_quickicon_privacycheck_url;
      var text = variables.plg_quickicon_privacycheck_text;
      var quickicon = document.getElementById('plg_quickicon_privacycheck');
      var link = quickicon.querySelector('span.j-links-link');
      Joomla.request({
        url: ajaxUrl,
        method: 'GET',
        data: '',
        perform: true,
        onSuccess: function onSuccess(response) {
          try {
            var request = JSON.parse(response);

            if (request.data.number_urgent_requests) {
              // Quickicon on dashboard shows message
              link.textContent = text.REQUESTFOUND + " " + request.data.number_urgent_requests; // Quickicon becomes red

              quickicon.classList.add('danger'); // Span in alert message

              var countSpan = document.createElement('span');
              countSpan.classList.add('label', 'label-important');
              countSpan.textContent = request.data.number_urgent_requests; // Button in alert to 'view requests'

              var requestButton = document.createElement('button');
              requestButton.classList.add('btn', 'btn-primary');
              requestButton.setAttribute('onclick', "document.location='" + url + "'");
              requestButton.textContent = text.REQUESTFOUND_BUTTON;
              var div = document.createElement('div');
              div.classList.add('alert', 'alert-error', 'alert-joomlaupdate');
              div.appendChild(countSpan);
              div.insertAdjacentText('beforeend', " " + text.REQUESTFOUND_MESSAGE);
              div.appendChild(requestButton); // Add elements to container for alert messages

              var container = document.querySelector('#system-message-container');
              container.insertBefore(div, container.firstChild);
            } else {
              quickicon.classList.add('success');
              link.textContent = text.NOREQUEST;
            }
          } catch (e) {
            quickicon.classList.add('danger');
            link.textContent = text.ERROR;
          }
        },
        onError: function onError() {
          quickicon.classList.add('danger');
          link.textContent = text.ERROR;
        }
      });
    });
  })(document);

}());
