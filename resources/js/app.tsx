import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    layout: () => AppLayout,
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#007A55FF',
    },
});
