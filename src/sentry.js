import * as Sentry from '@sentry/browser';

// All these values come from PHP templates
Sentry.init({
    dsn: sentrydsn,
    release: version,
});
Sentry.setUser({
    id: url,
    username: basepath,
});
