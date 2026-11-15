export default function (rootElement, siteRuntime) {
    const expandableRows = siteRuntime.queryAll('.accordion-item', rootElement);
    const rowToggleRemovers = expandableRows.map((expandableRow) => {
        const toggleRow = () => expandableRow.classList.toggle('is-open');
        expandableRow.addEventListener('click', toggleRow);
        return () => expandableRow.removeEventListener('click', toggleRow);
    });

    return () => rowToggleRemovers.forEach((removeToggle) => removeToggle());
}
