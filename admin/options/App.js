const useEffect = wp.element.useEffect;
const useState = wp.element.useState;

import {
  Card,
  Button,
  CardHeader,
  CardBody,
  CardFooter,
  TextControl,
  ToggleControl,
  Notice,
  __experimentalText as Text,
  __experimentalHeading as Heading,
} from '@wordpress/components';

const App = () => {

  const [postTypeName, setPostTypeName] = useState('');
  const [postTypeNameSingular, setPostTypeNameSingular] = useState('');
  const [postTypeSlug, setPostTypeSlug] = useState('');
  const [enableEditor, setEnableEditor] = useState(false);
  const [enableDetailPages, setEnableDetailPages] = useState(false);
  const [enableTaxonomy, setEnableTaxonomy] = useState(false);
  const [enableTaxonomyPage, setEnableTaxonomyPage] = useState(false);
  const [taxonomySlug, setTaxonomySlug] = useState('');
  const [enableCarouselBlock, setEnableCarouselBlock] = useState(false);
  const [enableGridBlock, setEnableGridBlock] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    /**
     * Initialize the options fields with the data received from the REST API
     * endpoint provided by the plugin.
     */
    wp.apiFetch({path: '/jobs-settings-page/v1/options'})
      .then(data => {
        //Set the new values of the options in the state
        setPostTypeName(data['post_type_name'] || '');
        setPostTypeNameSingular(data['post_type_name_singular'] || '');
        setPostTypeSlug(data['post_type_slug'] || '');
        setEnableEditor(!!data['enable_editor']);
        setEnableDetailPages(!!data['enable_detail_pages']);
        setEnableTaxonomy(!!data['enable_taxonomy']);
        setEnableTaxonomyPage(!!data['enable_taxonomy_page']);
        setTaxonomySlug(data['taxonomy_slug'] || '');
        setEnableCarouselBlock(!!data['enable_carousel_block']);
        setEnableGridBlock(!!data['enable_grid_block']);
        setIsLoading(false);
      })
      .catch(err => {
        setError('Failed to load options');
        setIsLoading(false);
      });
  }, []);

  if (isLoading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <Notice status="error">{error}</Notice>;
  }

  return (
        <Card style={{marginTop:20, marginRight:40}}>
          <CardHeader>
            <Heading level={ 4 }>Jobs Post Type Options</Heading>
          </CardHeader>
          <CardBody>
            <TextControl label="Post Type Name" type="text" value={ postTypeName } onChange={(state) => {
                    setPostTypeName(state);
                  }} />
            <TextControl label="Post Type Name Singlar" type="text" value={ postTypeNameSingular } onChange={(state) => {
                    setPostTypeNameSingular(state);
                  }} />
            <TextControl label="Post Type Slug" type="text" value={ postTypeSlug } onChange={(state) => {
                    setPostTypeSlug(state);
                  }} />
            <ToggleControl
              label="Enable Detail Pages"
              checked={ enableDetailPages }
              onChange={ () => setEnableDetailPages( ( state ) => ! state ) }
            />
            <ToggleControl
              label="Enabled the Block Editor"
              checked={ enableEditor }
              onChange={ () => setEnableEditor( ( state ) => ! state ) }
            />
          </CardBody>
          <CardHeader>
            <Heading level={ 4 }>Category Options</Heading>
          </CardHeader>
          <CardBody>
            <ToggleControl
              label="Enabled the Category Setup"
              checked={ enableTaxonomy }
              onChange={ () => setEnableTaxonomy( ( state ) => ! state ) }
            />
            <ToggleControl
              label="Enabled the Category Pages"
              checked={ enableTaxonomyPage }
              onChange={ () => setEnableTaxonomyPage( ( state ) => ! state ) }
            />
            <TextControl label="Taxonomy Slug" type="text"  value={ taxonomySlug } onChange={(state) => {
                setTaxonomySlug(state);
              }} />
          </CardBody>
          <CardHeader>
            <Heading level={ 4 }>Block Options</Heading>
          </CardHeader>
                    <CardBody>
            <ToggleControl
              label="Enabled the Carousel Block"
              checked={ enableCarouselBlock }
              onChange={ () => setEnableCarouselBlock( ( state ) => ! state ) }
            />
            <ToggleControl
              label="Enabled the Grid Block"
              checked={ enableGridBlock }
              onChange={ () => setEnableGridBlock( ( state ) => ! state ) }
            />
          </CardBody>
          <CardFooter>
            <Text>Save Jobs Options</Text>
          <Button
            variant="primary"
            onClick={() => {

              wp.apiFetch({
                path: '/jobs-settings-page/v1/options',
                method: 'POST',
                data: {
                  'post_type_name': postTypeName,
                  'post_type_name_singular': postTypeNameSingular,
                  'post_type_slug': postTypeSlug,
                  'enable_editor': enableEditor,
                  'enable_detail_pages': enableDetailPages,
                  'enable_taxonomy': enableTaxonomy,
                  'enable_taxonomy_page': enableTaxonomyPage,
                  'taxonomy_slug': taxonomySlug,
                  'enable_grid_block': enableGridBlock,
                  'enable_carousel_block': enableCarouselBlock,
                },
              }).then(data => {
                alert('Options saved successfully! Page will refresh on OK');
                document.getElementById('wp-admin-canonical').href = 'edit.php?post_type='+postTypeSlug+'&page=mctp-options';
                window.location.replace(document.getElementById('wp-admin-canonical').href);
              });

            }}>Save
          </Button>
        </CardFooter>
      </Card>

  );

};
export default App;