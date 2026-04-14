import wretch from 'wretch';

const client = wretch().options({
    credentials: 'same-origin',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

export default client;
