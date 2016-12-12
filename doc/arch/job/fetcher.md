## Fetcher

* The responsability of a data fetcher is to fetch data and save it to a tmp file.
* The file has to be streamable and usable by the reader class `Webbhuset\Bifrost\Utils\Reader\Interface`
* The fetcher returns the file name of the tmp file.