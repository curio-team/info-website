<div style="bottom: 0; right: 0; z-index: 1000; max-width: 50ch; position: fixed; padding: 1rem; border: 1px solid #ccc; background-color: #fff; border-start-start-radius: 1em;"
    id="__sdTestResults">
    <div style="display: flex; flex-direction: row; justify-content: space-between;">
        <h2 style="margin: 0;">Testresultaten <small>(<span id="status"></span>)</small></h2>
        <x-icons.show style="cursor: pointer;" onclick="testResultsEl.style.opacity = testResultsEl.style.opacity == 0.2 ? testResultsEl.style.opacity = 1 : testResultsEl.style.opacity = 0.2" />
    </div>
    <p>
        Deze testresultaten zijn alleen zichtbaar tijdens het testen. Zorg dat alle testen slagen voordat je de site inleverd.
    </p>
    <ul id="testResults"
        style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
        <template id="testResultTemplate">
            <li style="display: column; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <x-icons.checkmark class="checkmark" style="flex-shrink: 0;" />
                    <x-icons.checkmark-box-empty class="no-checkmark" style="flex-shrink: 0;" />
                    <span></span>
                </div>
                <ul class="test-data"></ul>
            </li>
        </template>
    </ul>
</div>

<script>
    var testResultsEl = document.getElementById('__sdTestResults');
    const extractedFiles = @json($extracted);
    let nextId = 0;

    function createPointerToElement(element) {
        const targetNode = document.createElement('a');
        const hasOwnId = !!element.id;
        if(!hasOwnId) {
            element.id = `__sdTestResults-pointer-${nextId++}`;
        }
        targetNode.setAttribute('href', '#');
        targetNode.setAttribute('onmouseover', `document.getElementById('${element.id}').style.outline = '2px solid red'; document.getElementById('${element.id}').style.opacity = '0.5';`);
        targetNode.setAttribute('onmouseout', `document.getElementById('${element.id}').style.outline = ''; document.getElementById('${element.id}').style.opacity = '';`);
        targetNode.textContent = element.tagName.toLowerCase() + (hasOwnId ? `#${element.id}` : '') + (element.className ? `.${element.className.split(' ').join('.')}` : '');
        return targetNode;
    }

    const tests = [
        // Test if all links work
        () => {
            const links = document.querySelectorAll('a');
            const brokenLinks = [];

            for (let i = 0; i < links.length; i++) {
                const link = links[i];
                const href = link.getAttribute('href');
                if (href && href.startsWith('http')) {
                    const request = new XMLHttpRequest();
                    request.open('GET', href, false);
                    request.send();
                    if (request.status === 404) {
                        brokenLinks.push(createPointerToElement(link));
                    }
                } else if (href && !extractedFiles.includes(href)) {
                    brokenLinks.push(createPointerToElement(link));
                }
            }

            return {
                passed: brokenLinks.length === 0,
                message: brokenLinks.length === 0 ? 'Alle links werken' : 'De volgende links werken niet: ',
                data: brokenLinks,
            }
        },

        // Test if the site has a horizontal scrollbar when the browser is 320px wide
        () => {
            const originalBodyWidth = document.body.style.width;
            document.body.style.width = '320px';

            let contentExceeding = [];
            const elements = document.querySelectorAll('*');

            // Test if any content exceeds the body width by adding x + border + padding + etc
            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];

                if(element.tagName === 'SCRIPT'
                || element.tagName === 'STYLE'
                || element.tagName === 'HTML'
                || element.tagName === 'HEAD'
                || element.tagName === 'BODY'
                || element.isEqualNode(testResultsEl)
                || testResultsEl.contains(element)) {
                    continue;
                }

                if (element.clientWidth > 320) {
                    contentExceeding.push(createPointerToElement(element));
                }
            }

            document.body.style.width = originalBodyWidth;

            return {
                passed: contentExceeding.length === 0,
                message: contentExceeding.length === 0 ? 'Responsiveness: De site heeft geen scrollbar wanneer de browser 320px breed is (kleine smartphone)' : 'Responsiveness: De volgende elementen geven de site een horizontale scrollbar wanneer de browser 320px breed is (kleine smartphone)',
                data: contentExceeding,
            }
        },
    ];

    // Writes test result to ul#testResults
    function writeTestResult(result) {
        const testResultTemplate = document.getElementById('testResultTemplate');
        const testResult = testResultTemplate.content.cloneNode(true);
        const testResultLi = testResult.querySelector('li');
        const testResultCheckmark = testResult.querySelector('.checkmark');
        const testResultNoCheckmark = testResult.querySelector('.no-checkmark');
        const testResultSpan = testResult.querySelector('span');
        const testData = testResult.querySelector('.test-data');

        testResultSpan.textContent = result.message;

        console.log(result);
        for (let i = 0; i < result.data.length; i++) {
            const testDataLi = document.createElement('li');
            testDataLi.appendChild(result.data[i]);
            testData.appendChild(testDataLi);
        }

        if (result.passed) {
            testResultNoCheckmark.remove();
        } else {
            testResultCheckmark.remove();
        }

        if (!result.passed)
            testResultLi.style.color = 'red';

        document.getElementById('testResults').appendChild(testResult);
    }

    // Execute all tests
    function runTests() {
        const passedTests = [];

        for (let i = 0; i < tests.length; i++) {
            const result = tests[i]();

            writeTestResult(result);

            if (result.passed)
                passedTests.push(result);
        }

        const testResultStatus = document.getElementById('status');
        if (passedTests.length !== tests.length) {
            testResultStatus.style.color = 'red';
            testResultStatus.textContent = passedTests.length + ' van de ' + tests.length + ' tests geslaagd';
        } else {
            testResultStatus.textContent = 'Alle tests geslaagd';
        }

    }

    // Run tests when page is loaded
    addEventListener('load', () => {
        runTests();
    });
</script>
