import * as Sentry from '@sentry/browser';

Sentry.init({
    dsn: sentrydsn,
    release: version,
});
Sentry.setUser({
    id: url,
    username: basepath,
});
