window.addEventListener('load', () => {

    const elSymbol = document.getElementById('symbol');
    const elQuoteTable = document.getElementById('all_quotes').querySelector('tbody');

    document.getElementById('request_quote').addEventListener('click', async () => {
        try {
            const url = request_quote_endpoint+'?symbol='+encodeURIComponent(elSymbol.value);
            const response = await fetch(url);
            const data = await response.json();
    
            const elQuoteRow = document.createElement('tr');
            elQuoteTable.insertBefore(elQuoteRow, elQuoteTable.firstChild);

            const countCell = document.createElement('td')
            countCell.textContent = '#';
            elQuoteRow.append(countCell);

            const dateCell = document.createElement('td')
            dateCell.textContent = data.datetime_request;
            elQuoteRow.append(dateCell);

            for (const property in data.quote) {
                const quoteCell = document.createElement('td');
                quoteCell.textContent = data.quote[property];
                quoteCell.setAttribute('tooltip', property);
                elQuoteRow.appendChild(quoteCell);
            }
        } catch (error) {
            errorHandler(error);
        }
    });

    document.getElementById('get_quote_requests').addEventListener('click', async () => {
        let quotesCount = elQuoteTable.rows.length
        try {
            const response = await fetch(get_quote_requests_endpoint+'?offset='+quotesCount);
            const data = await response.json();
            if (!data.length) {
                alert(intl_errors.empty_data);
            }

            data.forEach((quote) => {
                const elQuoteRow = document.createElement('tr');
                elQuoteTable.append(elQuoteRow);

                const countCell = document.createElement('td')
                countCell.textContent = ++quotesCount;
                elQuoteRow.append(countCell);

                const datetimeCell = document.createElement('td')
                datetimeCell.textContent = quote.datetime_request;
                elQuoteRow.append(datetimeCell);

                const temp = JSON.parse(quote.quote);
                for (const property in temp) {
                    const quoteCell = document.createElement('td');
                    quoteCell.textContent = temp[property];
                    quoteCell.setAttribute('tooltip', property);
                    elQuoteRow.appendChild(quoteCell);
                }
            });
        } catch (error) {
            errorHandler(error);
        }
    });
});

function errorHandler(error) {
    // for the time being, just warn the user
    alert(intl_errors.fetching_data);
}