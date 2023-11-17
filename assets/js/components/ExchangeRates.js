
import React, {Component, Fragment} from 'react';
import axios from 'axios';

/**
 * Component responsible for displaying exchange rates table
 */
class ExchangeRates extends Component {

    /**
     * @param {string} Contains current date as string in format YYYY-mm-dd
     */
    currDate;

    constructor() {
        super();

        this.handleDateChange = this.handleDateChange.bind(this);

        this.currDate = this.formatDate(new Date());

        this.state = { rates: {}, dateRates: {}, date: this.currDate, loading: true, dateLoading: false};
    }

    /**
     * Returns API Url
     *
     * @returns {string}
     */
    getAPIUrl() {
        return 'http://telemedi-zadanie.localhost/api/get-exchange-rates';
    }

    componentDidMount() {
        this.getExchangeRates();
        const search = this.props.location.search;
        const dateParam = this.parseDate(new URLSearchParams(search).get('date'));
        if (dateParam) {
            this.getExchangeRates(dateParam, 'dateRates', 'dateLoading');
            this.setState({date: dateParam});
        }
    }

    /**
     * Retrieves exchange rates from API
     *
     * @param date {string} Date of rates to get
     * @param name {string} Name of field in state Object to store rates
     * @param loadingName {string} Name of field containing info about loading process in progress
     */
    getExchangeRates(date = null, name = 'rates', loadingName = 'loading') {
        console.log('get rates for date: '+ date);
        let state = {};
        state[loadingName] = true;
        this.setState(state);
        let config = {params: {}};
        if (date) {
            config.params.date = date;
        }
        axios.get(this.getAPIUrl(), config).then(response => {
            console.log('got rates for date: '+ date);
            let rates = {};
            if (response.data && response.data.rates) {
                rates = response.data.rates;
            }
            let state = {};
            state[loadingName] = false;
            state[name] = rates;
            this.setState(state);
        }).catch(function (error) {
            console.error(error);
            let state = {};
            state[loadingName] = false;
            state[name] = {};
            this.setState(state);
        });
    }

    /**
     * Returns date in format YYYY-mm-dd
     * @param date {Date}
     * @returns {string}
     */
    formatDate(date) {
        return date.getFullYear() +'-'+ ((date.getMonth()+1).toString().padStart(2, '0')) +'-'+ date.getDate().toString().padStart(2, '0');
    }

    parseDate(dateStr) {
        let dateNum = Date.parse(dateStr);
        if (dateNum) {
            return this.formatDate(new Date(dateNum))
        }
        return false;
    }

    /**
     * Formats currency number
     *
     * @param currency {float}
     * @returns {string}
     */
    formatCurrency(currency) {
        return currency
            ? currency.toLocaleString(undefined, {
                minimumFractionDigits: 4,
                maximumFractionDigits: 4
            })
            : '';
    }

    /**
     * Date change handler. Initiates API request for chosen date.
     *
     * @param e {Event}
     */
    handleDateChange(e) {
        e.stopPropagation();
        let date = this.parseDate(e.target.value);
        if (date) {
            this.getExchangeRates(date, 'dateRates', 'dateLoading');
            this.setState({date: date});
            window.history.pushState(null, '', '/exchange-rates?date=' + date);
        }
    }

    /**
     * Renders part of table for one currency with set of rates (NBP, buy and sell).
     *
     * @param currency Object
     * @returns {JSX.Element}
     */
    renderCurrency(currency) {
        return (
            <>
                <td className={'nbp'}>{this.formatCurrency(currency.nbp)}</td>
                <td className={'buy'}>{this.formatCurrency(currency.buy)}</td>
                <td className={'sell'}>{this.formatCurrency(currency.sell)}</td>
            </>
        );
    }

    /**
     * Renders view
     * @returns {JSX.Element}
     */
    render() {
        const loading = this.state.loading || this.state.dateLoading;
        const showRatesForDate = this.state.dateRates
            && this.state.dateRates.date
            && this.state.dateRates.rates;

        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row mt-5">
                            <div className="col-md-8 offset-md-2">
                                <div className="datepicker text-center">
                                    <form>
                                        <div className="form-group row">
                                            <label className="col-sm-4 col-form-label" htmlFor="exchaneDate">Wyświetl kurs z dnia</label>
                                            <div className="col-sm-4">
                                            <input className="form-control"
                                                type={'date'} name={'currencyDate'}
                                                min={'2023-01-01'} max={this.currDate}
                                                value={this.state.date}
                                                pattern="\d{4}-\d{2}-\d{2}"
                                                onChange={this.handleDateChange}
                                                id="exchaneDate"
                                            />
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                {loading ? (
                                    <div className={'text-center'}>
                                        <span className="fa fa-spin fa-spinner fa-4x"></span>
                                    </div>
                                ) : (
                                    <div className={'text-center'}>
                                        <table className={'table table-light table-striped table-sm'}>
                                            <thead>
                                                <tr>
                                                    <th colSpan={2}>Waluta</th>
                                                    <th colSpan={3}>Kurs bieżący (z dnia {this.state.rates.date})</th>
                                                    {showRatesForDate ? (
                                                        <th colSpan={3}>Kurs z dnia {this.state.dateRates.date}</th>
                                                    ) : (
                                                        <></>
                                                    )}
                                                </tr>
                                                <tr>
                                                    <th>Kod</th>
                                                    <th>Nazwa</th>
                                                    <th>NBP</th>
                                                    <th>Kupno</th>
                                                    <th>Sprzedaż</th>
                                                    {showRatesForDate ? (
                                                        <>
                                                            <th>NBP</th>
                                                            <th>Kupno</th>
                                                            <th>Sprzedaż</th>
                                                        </>
                                                    ) : (
                                                        <></>
                                                    )}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            {Object.entries(this.state.rates.rates).map( ([index, currency]) =>
                                                <tr className={'currency'} key={index}>
                                                    <td className={'code'}>{currency.code}</td>
                                                    <td className={'name'}>{currency.name}</td>
                                                    {this.renderCurrency(currency)}
                                                    {showRatesForDate ? (
                                                        this.state.dateRates.rates[index] ?
                                                                this.renderCurrency(this.state.dateRates.rates[index])
                                                                : (
                                                                    <td colSpan={3}>brak danych</td>
                                                                )
                                                    ) : (
                                                        <></>
                                                    )
                                                    }
                                                </tr>
                                            )}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        )
    }
}
export default ExchangeRates;
