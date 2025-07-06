const {render} = wp.element;
import App from './App';

if (document.getElementById('mctp-react-app')) {
  render(<App/>, document.getElementById('mctp-react-app'));
}