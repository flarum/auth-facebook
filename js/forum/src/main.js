import { extend } from 'flarum/extend';
import app from 'flarum/app';
import LogInButtons from 'flarum/components/LogInButtons';
import LogInButton from 'flarum/components/LogInButton';

app.initializers.add('facebook', () => {
  extend(LogInButtons.prototype, 'items', function(items) {
    items.add('facebook',
      <LogInButton
        className="Button LogInButton--facebook"
        icon="facebook"
        path="/login/facebook">
        Log in with Facebook
      </LogInButton>
    );
  });
});
