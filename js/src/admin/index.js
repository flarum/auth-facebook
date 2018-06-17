import app from 'flarum/app';

import FacebookSettingsModal from './components/FacebookSettingsModal';

app.initializers.add('flarum-auth-facebook', () => {
  app.extensionSettings['flarum-auth-facebook'] = () => app.modal.show(new FacebookSettingsModal());
});
