<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Convention: drop your existing product photos into
     * storage/app/public/products/{category-slug}/{product-slug}/*.jpg
     * (any number of images, any filename) and this seeder will pick
     * them up automatically — no need to hardcode filenames here.
     */
    private array $catalog = [
        'women' => [
            [
                'name' => 'Tommy Jeans Half Zip Dress Black',
                'price' => 29900,
                'compare' => 39900,
                'sku' => 'W-001',
                'featured' => true,
                'short' => 'Half-zip dress with a modern, sporty edge.',
                'description' => 'This Tommy Jeans half-zip dress combines sporty details with feminine elegance. The half-zip front allows for versatile styling, while the premium cotton blend ensures all-day comfort. Perfect for casual outings or dressed up with accessories.'
            ],
            [
                'name' => 'Calvin Klein Monogram Tight Dress Black',
                'price' => 34900,
                'compare' => null,
                'sku' => 'W-002',
                'featured' => true,
                'short' => 'Body-hugging dress with iconic CK monogram.',
                'description' => 'Make a statement with this Calvin Klein bodycon dress featuring the iconic monogram pattern throughout. The figure-hugging silhouette accentuates your curves while the stretch fabric ensures a comfortable fit. Ideal for evening events or a night out.'
            ],
            [
                'name' => 'Hugo Polo Midi Dress Black',
                'price' => 27900,
                'compare' => 32900,
                'sku' => 'W-003',
                'featured' => false,
                'short' => 'Classic polo-style midi dress from Hugo.',
                'description' => 'This Hugo polo midi dress reimagines classic sportswear for modern sophistication. The polo collar and button placket add structure, while the midi length offers versatile styling options. Crafted from premium pique cotton for breathable comfort.'
            ],
            [
                'name' => 'Karl Lagerfeld Turtleneck Chain Belt Dress Black',
                'price' => 39900,
                'compare' => null,
                'sku' => 'W-004',
                'featured' => false,
                'short' => 'Chic turtleneck dress with signature chain belt.',
                'description' => 'Elegance meets edge in this Karl Lagerfeld turtleneck dress. The high neckline provides sophisticated coverage, while the signature chain belt cinches the waist for a flattering silhouette. A versatile piece that transitions seamlessly from day to night.'
            ],
            [
                'name' => 'Calvin Klein Jeans Monogram Sweatshirt Dress Black',
                'price' => 25900,
                'compare' => null,
                'sku' => 'W-005',
                'featured' => false,
                'short' => 'Casual sweatshirt dress with bold monogram print.',
                'description' => 'Stay comfortable and stylish with this Calvin Klein Jeans sweatshirt dress. The bold monogram print adds a contemporary touch to the relaxed silhouette, while the soft cotton fleece ensures cozy warmth. Perfect for running errands or casual weekends.'
            ],
            [
                'name' => 'Tommy Hilfiger Pleated Polo Dress Navy',
                'price' => 28900,
                'compare' => 35900,
                'sku' => 'W-006',
                'featured' => false,
                'short' => 'Navy polo dress with feminine pleated skirt.',
                'description' => 'This Tommy Hilfiger dress combines preppy polo styling with a feminine pleated skirt. The navy blue color offers versatility, while the pleated detail adds movement and elegance. Crafted from breathable cotton for all-day comfort.'
            ],
            [
                'name' => 'Boss V-Neck Belted Midi Dress Navy',
                'price' => 37900,
                'compare' => null,
                'sku' => 'W-007',
                'featured' => false,
                'short' => 'Elegant belted midi dress from Boss.',
                'description' => 'Sophisticated and refined, this Boss belted midi dress epitomizes modern elegance. The V-neckline and self-tie belt create a flattering silhouette, while the navy hue ensures versatile styling. Perfect for the office or special occasions.'
            ],
            [
                'name' => 'Tommy Jeans Pleated Shirt Dress Navy',
                'price' => 26900,
                'compare' => null,
                'sku' => 'W-008',
                'featured' => false,
                'short' => 'Button-down shirt dress with pleated details.',
                'description' => 'This Tommy Jeans shirt dress offers effortless style with its button-down front and pleated skirt. The navy blue color provides a classic foundation, while the pleats add texture and movement. A wardrobe essential for any season.'
            ],
            [
                'name' => 'Calvin Klein Button Up Ribbed Midi Dress Black',
                'price' => 31900,
                'compare' => 38900,
                'sku' => 'W-009',
                'featured' => false,
                'short' => 'Ribbed midi dress with button-down front.',
                'description' => 'This Calvin Klein ribbed midi dress combines comfort with sophistication. The button-down front allows for adjustable styling, while the ribbed knit offers a flattering, body-conscious fit. The versatile black color ensures year-round wearability.'
            ],
            [
                'name' => 'Guess Mock Neck Side Slit Dress Black',
                'price' => 28900,
                'compare' => null,
                'sku' => 'W-010',
                'featured' => false,
                'short' => 'Daring mock neck dress with side slit detail.',
                'description' => 'Make a bold statement with this Guess mock neck dress. The side slit adds a touch of allure while maintaining sophistication, and the figure-flattering silhouette ensures you\'ll turn heads. Perfect for date night or special events.'
            ],
            [
                'name' => 'Guess One Shoulder Glittery Midi Dress Silver',
                'price' => 35900,
                'compare' => 42900,
                'sku' => 'W-011',
                'featured' => true,
                'short' => 'Stunning one-shoulder dress with glitter finish.',
                'description' => 'Shine bright in this Guess one-shoulder midi dress. The glittery silver fabric catches the light beautifully, while the asymmetrical neckline adds contemporary drama. Ideal for parties, galas, and any occasion that calls for glamour.'
            ],
            [
                'name' => 'Tommy Hilfiger Turtleneck Knit Dress Brown',
                'price' => 29900,
                'compare' => null,
                'sku' => 'W-012',
                'featured' => false,
                'short' => 'Cozy turtleneck knit dress in rich brown.',
                'description' => 'Stay warm and stylish with this Tommy Hilfiger turtleneck knit dress. The rich brown color adds warmth to your wardrobe, while the cozy knit provides comfort without sacrificing style. Perfect for autumn and winter layering.'
            ],
            [
                'name' => 'Calvin Klein Face Graphic Maxi Dress Grey',
                'price' => 32900,
                'compare' => null,
                'sku' => 'W-013',
                'featured' => false,
                'short' => 'Maxi dress with bold face graphic print.',
                'description' => 'Make a statement with this Calvin Klein maxi dress featuring a bold face graphic. The floor-length silhouette offers dramatic elegance, while the grey color provides a modern, neutral foundation. Perfect for making an impression.'
            ],
            [
                'name' => 'Hugo Padded Shoulder Gathered Mini Dress Black',
                'price' => 34900,
                'compare' => 39900,
                'sku' => 'W-014',
                'featured' => false,
                'short' => 'Mini dress with structured padded shoulders.',
                'description' => 'This Hugo mini dress combines power dressing with feminine charm. The padded shoulders add structure and confidence, while the gathered details create a flattering shape. A bold choice for any fashion-forward event.'
            ],
            [
                'name' => 'Michael Kors Short Sleeve Shirt Dress with Chain Belt Black',
                'price' => 38900,
                'compare' => null,
                'sku' => 'W-015',
                'featured' => false,
                'short' => 'Shirt dress featuring signature chain belt.',
                'description' => 'Elevate your wardrobe with this Michael Kors shirt dress. The iconic chain belt cinches the waist for a figure-flattering silhouette, while the short sleeves offer versatility for any season. Crafted from premium fabric for lasting quality.'
            ],
            [
                'name' => 'Karl Lagerfeld Wrap Mini Dress Swirl Logo Black',
                'price' => 36900,
                'compare' => 44900,
                'sku' => 'W-016',
                'featured' => false,
                'short' => 'Wrap mini dress with swirl logo pattern.',
                'description' => 'This Karl Lagerfeld wrap mini dress features a distinctive swirl logo pattern that sets it apart. The wrap design creates a universally flattering silhouette, while the mini length adds a touch of playfulness. A statement piece for any wardrobe.'
            ],
            [
                'name' => 'Guess Cut Out Sweetheart Neck Dress Black',
                'price' => 27900,
                'compare' => null,
                'sku' => 'W-017',
                'featured' => false,
                'short' => 'Romantic sweetheart neckline with cut-out detail.',
                'description' => 'This Guess dress combines romance with edge through its sweetheart neckline and strategic cut-out detail. The figure-skimming silhouette ensures a flattering fit, while the black color provides a classic, versatile foundation.'
            ],
            [
                'name' => 'Michael Kors Leopard Print V-Neck Dress Brown',
                'price' => 39900,
                'compare' => null,
                'sku' => 'W-018',
                'featured' => true,
                'short' => 'Wild leopard print dress with flattering V-neck.',
                'description' => 'Make a fierce statement with this Michael Kors leopard print dress. The V-neckline creates a lengthening effect, while the animal print adds boldness and personality. The rich brown tones ensure sophisticated styling.'
            ],
            [
                'name' => 'Liu Jo Ribbed Knit Turtleneck Dress Beige',
                'price' => 25900,
                'compare' => null,
                'sku' => 'W-019',
                'featured' => false,
                'short' => 'Ribbed knit turtleneck dress in versatile beige.',
                'description' => 'This Liu Jo ribbed knit dress offers classic sophistication with its elegant turtleneck and versatile beige color. The ribbed texture adds visual interest, while the comfortable knit ensures all-day wearability. A timeless addition to any wardrobe.'
            ],
            [
                'name' => 'Guess Corset Style Zipper Front Midi Dress Black',
                'price' => 30900,
                'compare' => 36900,
                'sku' => 'W-020',
                'featured' => false,
                'short' => 'Corset-inspired midi dress with front zipper.',
                'description' => 'This Guess corset-style midi dress combines structural design with contemporary edge. The front zipper adds a modern touch, while the corset-inspired silhouette cinches the waist for a flattering shape. Perfect for making a fashion statement.'
            ],
        ],
        'men' => [
            [
                'name' => 'Men Slim Fit Grey Trousers',
                'price' => 24900,
                'compare' => null,
                'sku' => 'M-001',
                'featured' => true,
                'short' => 'Slim fit trousers in versatile grey.',
                'description' => 'These slim fit grey trousers offer a contemporary silhouette that works for any occasion. The comfortable stretch fabric ensures easy movement while maintaining a sharp, tailored appearance. Perfect for both office and casual wear.'
            ],
            [
                'name' => 'Men Dark Grey Patterned Suit',
                'price' => 49900,
                'compare' => 59900,
                'sku' => 'M-002',
                'featured' => true,
                'short' => 'Suit with subtle pattern in dark grey.',
                'description' => 'This dark grey patterned suit offers sophisticated style for the modern man. The subtle pattern adds visual interest without being overpowering, while the tailored fit ensures a sharp, professional appearance. Perfect for business or formal events.'
            ],
            [
                'name' => 'Men Navy Blue Slim Fit Suit',
                'price' => 47900,
                'compare' => null,
                'sku' => 'M-003',
                'featured' => false,
                'short' => 'Classic navy suit with slim fit cut.',
                'description' => 'This navy blue slim fit suit is a wardrobe essential for any stylish man. The classic color offers versatility for any occasion, while the slim fit provides a modern silhouette. Crafted from premium fabric for lasting quality.'
            ],
            [
                'name' => 'Men Textured Charcoal Grey Suit',
                'price' => 45900,
                'compare' => 54900,
                'sku' => 'M-004',
                'featured' => false,
                'short' => 'Textured suit in charcoal grey.',
                'description' => 'Add depth to your wardrobe with this textured charcoal grey suit. The subtle texture adds visual interest, while the rich grey color ensures versatile styling. The tailored cut offers a contemporary silhouette for the modern man.'
            ],
            [
                'name' => 'Men Beige Blazer with Black Pants Outfit',
                'price' => 38900,
                'compare' => null,
                'sku' => 'M-005',
                'featured' => false,
                'short' => 'Beige blazer paired with black trousers.',
                'description' => 'This versatile beige blazer and black pants combination offers effortless style. The neutral blazer provides a sophisticated foundation, while the black trousers create a sleek, elongating silhouette. A smart-casual essential for any wardrobe.'
            ],
            [
                'name' => 'Men Light Blue Summer Suit',
                'price' => 42900,
                'compare' => 48900,
                'sku' => 'M-006',
                'featured' => false,
                'short' => 'Fresh summer suit in light blue.',
                'description' => 'Stay cool and stylish with this light blue summer suit. The lightweight fabric ensures breathability for warm weather, while the fresh color adds a contemporary touch. Perfect for summer weddings, events, or any occasion that calls for refined style.'
            ],
            [
                'name' => 'Men Light Grey Suit with Patterned Shirt',
                'price' => 44900,
                'compare' => null,
                'sku' => 'M-007',
                'featured' => false,
                'short' => 'Light grey suit complete with patterned shirt.',
                'description' => 'This light grey suit set includes a patterned shirt for a complete, coordinated look. The versatile grey color offers endless styling possibilities, while the patterned shirt adds personality and flair to your ensemble.'
            ],
            [
                'name' => 'Men Classic Tan Beige Suit',
                'price' => 46900,
                'compare' => 55900,
                'sku' => 'M-008',
                'featured' => false,
                'short' => 'Timeless tan beige suit.',
                'description' => 'A classic tan beige suit that transcends seasonal trends. The timeless color offers versatility for any occasion, while the traditional cut provides a sophisticated silhouette. A must-have for any stylish gentleman\'s wardrobe.'
            ],
            [
                'name' => 'Men Three Piece Beige Suit',
                'price' => 51900,
                'compare' => null,
                'sku' => 'M-009',
                'featured' => true,
                'short' => 'Three-piece beige suit for formal elegance.',
                'description' => 'This three-piece beige suit offers the ultimate in formal elegance. The addition of the waistcoat elevates the ensemble, while the classic beige color ensures versatile styling. Perfect for weddings, formal events, and special occasions.'
            ],
            [
                'name' => 'Men Grey Plaid Blazer with Beige Trousers',
                'price' => 39900,
                'compare' => 46900,
                'sku' => 'M-010',
                'featured' => false,
                'short' => 'Grey plaid blazer paired with beige trousers.',
                'description' => 'Make a statement with this grey plaid blazer paired with beige trousers. The plaid pattern adds visual interest, while the beige trousers create a balanced, versatile base. A sophisticated choice for any occasion.'
            ],
            [
                'name' => 'Men Grey Windowpane Check Suit Black Shirt',
                'price' => 48900,
                'compare' => null,
                'sku' => 'M-011',
                'featured' => false,
                'short' => 'Windowpane check suit with black shirt.',
                'description' => 'This windowpane check suit with a black shirt offers a bold, contemporary look. The check pattern provides a modern twist on classic suiting, while the black shirt adds a sleek, sophisticated edge. Perfect for making a statement.'
            ],
            [
                'name' => 'Men Blue Textured Suit Paisley Shirt',
                'price' => 46900,
                'compare' => 54900,
                'sku' => 'M-012',
                'featured' => false,
                'short' => 'Blue textured suit with paisley shirt.',
                'description' => 'This blue textured suit set includes a paisley shirt for a complete, coordinated look. The textured fabric adds depth and interest, while the paisley pattern offers a touch of classic elegance. A sophisticated choice for any gentleman.'
            ],
            [
                'name' => 'Men Black Overcoat Burgundy Turtleneck Outfit',
                'price' => 55900,
                'compare' => null,
                'sku' => 'M-013',
                'featured' => true,
                'short' => 'Black overcoat layered over burgundy turtleneck.',
                'description' => 'This sophisticated outfit features a black overcoat layered over a rich burgundy turtleneck. The monochromatic base is elevated with a pop of color, creating a refined and fashion-forward ensemble that commands attention.'
            ],
            [
                'name' => 'Men Classic Navy Blue Suit Grey Tie',
                'price' => 43900,
                'compare' => 51900,
                'sku' => 'M-014',
                'featured' => false,
                'short' => 'Classic navy suit with coordinating grey tie.',
                'description' => 'This classic navy blue suit set includes a coordinating grey tie for a complete, polished look. The timeless navy color offers versatility, while the grey tie adds a touch of sophistication. A must-have for any business wardrobe.'
            ],
            [
                'name' => 'Men Sharp Black Suit White Shirt',
                'price' => 45900,
                'compare' => null,
                'sku' => 'M-015',
                'featured' => false,
                'short' => 'Sharp black suit with crisp white shirt.',
                'description' => 'The ultimate classic: a sharp black suit with a crisp white shirt. This timeless combination offers sophistication and elegance for any formal occasion. The sharp tailoring ensures a flawless, confidence-boosting fit.'
            ],
            [
                'name' => 'Men Casual Black Suit Patterned Shirt Sneakers',
                'price' => 39900,
                'compare' => 45900,
                'sku' => 'M-016',
                'featured' => false,
                'short' => 'Casual black suit with patterned shirt and sneakers.',
                'description' => 'Reinvent the suit with this casual black suit styled with a patterned shirt and sneakers. This modern approach to suiting offers comfort without sacrificing style, perfect for contemporary events and fashion-forward gentlemen.'
            ],
            [
                'name' => 'Men Charcoal Grey Suit Light Blue Shirt',
                'price' => 44900,
                'compare' => null,
                'sku' => 'M-017',
                'featured' => false,
                'short' => 'Charcoal grey suit with light blue shirt.',
                'description' => 'This charcoal grey suit paired with a light blue shirt offers a refined, professional look. The dark grey provides a sophisticated base, while the light blue adds a subtle pop of color. Perfect for the office and formal occasions.'
            ],
            [
                'name' => 'Men Dark Navy Suit Patterned Grey Shirt',
                'price' => 47900,
                'compare' => 56900,
                'sku' => 'M-018',
                'featured' => false,
                'short' => 'Dark navy suit with patterned grey shirt.',
                'description' => 'This dark navy suit set includes a patterned grey shirt for a distinctive, coordinated look. The rich navy color offers sophistication, while the patterned shirt adds personality and visual interest to your ensemble.'
            ],
            [
                'name' => 'Men Elegant Black Velvet Tuxedo',
                'price' => 59900,
                'compare' => null,
                'sku' => 'M-019',
                'featured' => true,
                'short' => 'Luxurious black velvet tuxedo.',
                'description' => 'Make a grand entrance with this luxurious black velvet tuxedo. The sumptuous velvet fabric catches the light beautifully, creating a dramatic, sophisticated silhouette. Ideal for black-tie events, galas, and the most formal of occasions.'
            ],
            [
                'name' => 'Men Navy Blue Blazer Beige Chino Pants',
                'price' => 32900,
                'compare' => 38900,
                'sku' => 'M-020',
                'featured' => false,
                'short' => 'Navy blazer paired with beige chinos.',
                'description' => 'This versatile combination of navy blue blazer and beige chino pants offers classic, effortless style. The timeless pairing works for any occasion, while the comfortable chinos ensure relaxed, all-day wear. A smart-casual essential.'
            ],
        ],
        'accessories' => [
            [
                'name' => 'Tommy Hilfiger Black Monogram Card Holder',
                'price' => 12900,
                'compare' => null,
                'sku' => 'A-001',
                'featured' => false,
                'short' => 'Slim card holder with monogram print.',
                'description' => 'This Tommy Hilfiger card holder combines style with functionality. The slim design fits perfectly in any pocket, while the monogram print adds a touch of brand sophistication. Crafted from premium materials for lasting durability.'
            ],
            [
                'name' => 'Tommy Hilfiger Black Monogram Makeup Bag',
                'price' => 15900,
                'compare' => 19900,
                'sku' => 'A-002',
                'featured' => false,
                'short' => 'Stylish makeup bag with iconic monogram.',
                'description' => 'Keep your essentials organized with this Tommy Hilfiger makeup bag. The iconic monogram pattern adds brand recognition, while the spacious interior offers practical storage. The durable construction ensures long-lasting use.'
            ],
            [
                'name' => 'Coach Signature Canvas Leather Gloves',
                'price' => 19900,
                'compare' => null,
                'sku' => 'A-003',
                'featured' => false,
                'short' => 'Signature canvas gloves with leather trim.',
                'description' => 'Stay warm in style with these Coach gloves featuring signature canvas and leather trim. The classic design offers timeless appeal, while the quality materials ensure warmth and durability. A must-have accessory for cooler months.'
            ],
            [
                'name' => 'Guess Black Knit Beanie and Scarf Set',
                'price' => 18900,
                'compare' => 24900,
                'sku' => 'A-004',
                'featured' => false,
                'short' => 'Matching beanie and scarf set in black.',
                'description' => 'This coordinated Guess beanie and scarf set offers warmth and style for cold weather. The soft knit ensures comfort, while the versatile black color complements any outerwear. A practical and fashionable winter essential.'
            ],
            [
                'name' => 'Coach White Cable Knit Beanie Hat',
                'price' => 14900,
                'compare' => null,
                'sku' => 'A-005',
                'featured' => false,
                'short' => 'White cable knit beanie from Coach.',
                'description' => 'Add a touch of winter elegance with this Coach cable knit beanie. The white color offers a fresh, clean look, while the cable knit design adds classic texture. Crafted from premium materials for exceptional warmth and comfort.'
            ],
            [
                'name' => 'Pinko Brown Leather Belt Gold Birds Buckle',
                'price' => 22900,
                'compare' => 28900,
                'sku' => 'A-006',
                'featured' => true,
                'short' => 'Brown leather belt with signature bird buckle.',
                'description' => 'This Pinko belt features a distinctive gold birds buckle that serves as a signature statement piece. The rich brown leather offers versatility, while the unique buckle adds a touch of playful luxury to any outfit.'
            ],
            [
                'name' => 'Karl Lagerfeld Black Baseball Cap',
                'price' => 12900,
                'compare' => null,
                'sku' => 'A-007',
                'featured' => false,
                'short' => 'Black baseball cap with subtle branding.',
                'description' => 'This Karl Lagerfeld baseball cap offers street-style sophistication. The classic black color ensures versatility, while the subtle branding adds a touch of designer appeal. Perfect for casual, effortless styling.'
            ],
            [
                'name' => 'Karl Lagerfeld Black Umbrella with Logo Pattern',
                'price' => 17900,
                'compare' => 22900,
                'sku' => 'A-008',
                'featured' => false,
                'short' => 'Black umbrella featuring logo pattern.',
                'description' => 'Stay dry in style with this Karl Lagerfeld umbrella featuring a distinctive logo pattern. The black canopy offers classic appeal, while the logo pattern adds a touch of designer sophistication. A practical and fashionable accessory for rainy days.'
            ],
            [
                'name' => 'Tory Burch Gold Heart Pendant Necklace',
                'price' => 24900,
                'compare' => null,
                'sku' => 'A-009',
                'featured' => true,
                'short' => 'Gold heart pendant necklace from Tory Burch.',
                'description' => 'Add a touch of romance with this Tory Burch gold heart pendant necklace. The classic gold finish offers timeless appeal, while the heart pendant adds a sentimental touch. A versatile accessory for any jewelry collection.'
            ],
            [
                'name' => 'Tory Burch Gold Interlocking Rings Necklace',
                'price' => 26900,
                'compare' => 32900,
                'sku' => 'A-010',
                'featured' => false,
                'short' => 'Modern necklace with interlocking rings.',
                'description' => 'This Tory Burch necklace features interlocking gold rings for a modern, contemporary look. The unique design offers visual interest, while the gold finish ensures timeless appeal. A standout piece for any occasion.'
            ],
            [
                'name' => 'Calvin Klein Black Leather Bifold Wallet',
                'price' => 18900,
                'compare' => null,
                'sku' => 'A-011',
                'featured' => false,
                'short' => 'Classic black leather bifold wallet.',
                'description' => 'This Calvin Klein bifold wallet offers classic sophistication and practical functionality. The premium black leather ensures durability, while the compact design fits comfortably in any pocket. A wardrobe essential for the modern man.'
            ],
            [
                'name' => 'Calvin Klein Black Monogram Bifold Wallet',
                'price' => 19900,
                'compare' => 24900,
                'sku' => 'A-012',
                'featured' => false,
                'short' => 'Bifold wallet with monogram pattern.',
                'description' => 'This Calvin Klein bifold wallet features a distinctive monogram pattern for added style. The compact design offers practical storage, while the durable construction ensures long-lasting use. A stylish essential for any wardrobe.'
            ],
            [
                'name' => 'Just Cavalli Black Leather Belt Monogram Buckle',
                'price' => 20900,
                'compare' => null,
                'sku' => 'A-013',
                'featured' => false,
                'short' => 'Black leather belt with monogram buckle.',
                'description' => 'This Just Cavalli belt features a distinctive monogram buckle that serves as a signature statement piece. The premium black leather offers versatility, while the monogram design adds a touch of brand sophistication.'
            ],
            [
                'name' => 'Tommy Hilfiger Navy Blue Baseball Cap',
                'price' => 11900,
                'compare' => null,
                'sku' => 'A-014',
                'featured' => false,
                'short' => 'Navy blue baseball cap with brand logo.',
                'description' => 'This Tommy Hilfiger baseball cap offers classic American style. The navy blue color offers versatility, while the brand logo adds a touch of authenticity. A casual essential for any wardrobe.'
            ],
            [
                'name' => 'Armani Exchange Black Wash Bag Toiletries',
                'price' => 16900,
                'compare' => 21900,
                'sku' => 'A-015',
                'featured' => false,
                'short' => 'Black wash bag for toiletries.',
                'description' => 'This Armani Exchange wash bag offers practical organization for your toiletries. The sleek black design offers understated elegance, while the spacious interior provides ample storage. A travel essential for the modern individual.'
            ],
            [
                'name' => 'Boss Hugo Boss Dark Red Patterned Pocket Square',
                'price' => 9900,
                'compare' => null,
                'sku' => 'A-016',
                'featured' => false,
                'short' => 'Dark red pocket square with pattern.',
                'description' => 'Add a pop of color with this Boss pocket square in dark red with subtle pattern. The premium fabric ensures a crisp fold, while the distinctive color adds personality to any suit. A must-have accessory for formal occasions.'
            ],
            [
                'name' => 'Boss Hugo Boss Beige Cotton Towel',
                'price' => 14900,
                'compare' => 18900,
                'sku' => 'A-017',
                'featured' => false,
                'short' => 'Beige cotton towel from Boss.',
                'description' => 'This Boss cotton towel offers luxurious softness and absorbency. The premium cotton ensures gentle care for your skin, while the elegant beige color adds a touch of sophistication to any bathroom. A quality essential for daily use.'
            ],
            [
                'name' => 'Boss Hugo Boss Navy Blue Cotton Towel',
                'price' => 14900,
                'compare' => 18900,
                'sku' => 'A-018',
                'featured' => false,
                'short' => 'Navy blue cotton towel from Boss.',
                'description' => 'This Boss cotton towel offers luxurious softness and absorbency. The premium cotton ensures gentle care for your skin, while the rich navy blue color adds depth to any bathroom decor. A quality essential for daily use.'
            ],
            [
                'name' => 'Dsquared2 Black Baseball Cap White Icon Logo',
                'price' => 15900,
                'compare' => null,
                'sku' => 'A-019',
                'featured' => false,
                'short' => 'Black baseball cap with white logo.',
                'description' => 'This Dsquared2 baseball cap features a bold white icon logo against a black base for maximum impact. The classic silhouette ensures versatile styling, while the contrast logo adds a fashion-forward statement. Perfect for casual wear.'
            ],
            [
                'name' => 'Calvin Klein Black Monogram Wash Bag',
                'price' => 16900,
                'compare' => 21900,
                'sku' => 'A-020',
                'featured' => false,
                'short' => 'Wash bag with monogram pattern.',
                'description' => 'This Calvin Klein wash bag combines style with practicality. The monogram pattern adds a touch of brand sophistication, while the spacious interior offers ample storage for toiletries. A travel essential with style.'
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->catalog as $categorySlug => $products) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();

            foreach ($products as $data) {
                $slug = Str::slug($data['name']);

                $product = Product::updateOrCreate(
                    ['sku' => $data['sku']],
                    [
                        'category_id' => $category->id,
                        'name' => $data['name'],
                        'slug' => $slug,
                        'short_description' => $data['short'],
                        'description' => $data['description'],
                        'price_cents' => $data['price'],
                        'compare_at_price_cents' => $data['compare'],
                        'is_published' => true,
                        'is_featured' => $data['featured'],
                        'published_at' => now(),
                    ]
                );

                $this->attachSizes($product);
                $this->attachImages($product, $categorySlug, $slug);
            }
        }
    }

    private function attachSizes(Product $product): void
    {
        foreach (['S', 'M', 'L', 'XL'] as $size) {
            $product->variants()->updateOrCreate(
                ['size' => $size],
                ['stock' => random_int(0, 25)]
            );
        }
    }

    private function attachImages(Product $product, string $categorySlug, string $slug): void
    {
        $dir = "products/{$categorySlug}/{$slug}";

        if ($product->images()->exists()) {
            return; // already seeded once, don't duplicate on re-run
        }

        $files = Storage::disk('public')->exists($dir)
            ? Storage::disk('public')->files($dir)
            : [];

        if (empty($files)) {
            $this->command?->warn("No images found in storage/app/public/{$dir} — copy your product photos there and re-seed.");

            return;
        }

        foreach (array_values($files) as $index => $path) {
            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }
}
