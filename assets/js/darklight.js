var darkLightCookie = getCookie("darklightswitch");
if (darkLightCookie) {
  setDarkStyle('uk-light', 'LightSwitch', 'DarkSwitch');
} else {
  unsetDarkStyle('uk-light', 'DarkSwitch', 'LightSwitch');
}
document.querySelectorAll('.darklight-switch').forEach(item => {
  item.addEventListener('mousedown', event => {
    event.preventDefault();
    var darkLightCookie = getCookie("darklightswitch");
    if (darkLightCookie != "" && darkLightCookie != null) {
      unsetCookie('darklightswitch');
      unsetDarkStyle('uk-light', 'DarkSwitch', 'LightSwitch');
    } else {
      setCookie('darklightswitch', 'dark', 7);
      setDarkStyle('uk-light', 'LightSwitch', 'DarkSwitch');
    }
  })
})