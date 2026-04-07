import wretch from 'wretch';

const client = wretch().options({
    credentials: 'same-origin', // ważne przy sesji Laravel
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

export default client;
