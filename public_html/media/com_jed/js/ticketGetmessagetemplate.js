//**
// * @package       JED
// *
// * @subpackage    Tickets
// *
// * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
// * @license       GNU General Public License version 2 or later; see LICENSE.txt
// */

let templates = document.getElementById("jform_messagetemplates"); //The Joomla SQL Field
let textbox = document.getElementById("jform_message_text"); //TinyMCE field
textbox.textContent = 'Select a template from above or start typing!';
templates.addEventListener('change', function () {

    //   alert(textbox.textContent);
    tinyMCE.get('jform_message_text').execCommand('SelectAll');
    tinyMCE.get('jform_message_text').execCommand('Delete');

    tinyMCE.get('jform_message_text').execCommand('mceInsertContent', false, 'Fetching content - Please Wait!');
    setMessageContent('Jedticket.getTemplate', templates.value)
})

async function setMessageContent(itemTask, itemId) {
    const token = Joomla.getOptions('csrf.token', '');
    let url = 'index.php?option=com_jed&task=' + itemTask + '&format=raw';

    let data = new URLSearchParams();
    data.append(`itemId`, itemId);
    data.append(token, '1');
    const options = {
        method: 'POST',
        body: data
    }
    //alert(itemId);
    let response = await fetch(url, options);
    if (!response.ok) {
        throw new Error(Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS') + `${response.status}`);
    } else {
        let result = await response.text();

        let myArr = result.split("|");
        tinyMCE.get('jform_message_text').execCommand('SelectAll');
        tinyMCE.get('jform_message_text').execCommand('Delete');
        tinyMCE.get('jform_message_text').execCommand('mceInsertContent', false, myArr[1]);
        let subjecttextbox = document.getElementById("jform_message_subject");
        subjecttextbox.value = myArr[0];

    }
}


