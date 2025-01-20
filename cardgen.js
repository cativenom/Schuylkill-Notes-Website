
let i = 0;

function createInnerHTML(content, result) {
    return content += `
    <div class="card">
    <img src="${result.filename}" alt="Thumbnail Image" style="width:100%">
        <div class = "container">
            <h2>Title: ${result.firstLast}</h2>
            <h2>Location Found: ${result.location}</h2>
            <h2>Date Found: ${result.dateFound}</h2>
            <h2>Store/Park: ${result.store}</h2>
            <h2>Found in: ${result.container}</h2>
            <h2>Theme: ${result.theme}</h2>
        </div>
    </div>
    `;
}
function createElements(content) {
    container = document.createElement('card');
    container.innerHTML += content;
    document.body.append(container);
}

let content0 = `<div class = "column0">
                <div class = "row">`
let content1 = `<div class = "column">
                <div class = "row">`
let content2 = `<div class = "column">
                <div class = "row">`
cardContent.forEach((result) => {
    switch(i) {
        case 0:
            content0 = createInnerHTML(content0, result)
            i++
            break;
        case 1:
            content1 = createInnerHTML(content1, result)
            i++
            break;
        case 2:
            content2 = createInnerHTML(content2, result)
            i = 0;
            break;
    }
});
        content0 += `</div>
        </div>`
        content1 += `</div>
        </div>`
        content2 += `</div>
        </div>`



createElements(content0)
createElements(content1)
createElements(content2)

