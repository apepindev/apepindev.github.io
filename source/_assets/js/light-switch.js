const lightSwitches = document.querySelectorAll('.light-switch');

if (lightSwitches.length > 0) {
    lightSwitches.forEach((lightSwitch, i) => {
        if (localStorage.getItem('dark-mode') === 'true') {
            lightSwitch.checked = true;
        }

        lightSwitch.addEventListener('change', () => {
            const { checked } = lightSwitch;

            lightSwitches.forEach((el, n) => {
                if (n !== i) {
                    el.checked = checked;
                }
            });

            if (lightSwitch.checked) {
                document.documentElement.classList.add('dark');
                document.querySelectorAll('.light-switch-sun').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.light-switch-moon').forEach(el => el.classList.remove('hidden'));
                localStorage.setItem('dark-mode', true);
            } else {
                document.documentElement.classList.remove('dark');
                document.querySelectorAll('.light-switch-sun').forEach(el => el.classList.remove('hidden'));
                document.querySelectorAll('.light-switch-moon').forEach(el => el.classList.add('hidden'));
                localStorage.setItem('dark-mode', false);
            }
        });
    });
}