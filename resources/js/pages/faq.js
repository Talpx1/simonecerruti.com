export default function (el, SC) {
    const rows = SC.$$('.xrow', el);
    const toggles = rows.map((row) => {
        const toggle = () => row.classList.toggle('open');
        row.addEventListener('click', toggle);
        return () => row.removeEventListener('click', toggle);
    });

    return () => toggles.forEach((off) => off());
}
