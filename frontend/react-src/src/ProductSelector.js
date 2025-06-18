import React, { useState } from 'react';
import axios from 'axios';

const ProductSelector = () => {
    const [category, setCategory] = useState('');
    const [products, setProducts] = useState([]);
    const [error, setError] = useState('');

    const handleCategoryChange = async (e) => {
        const selectedCategory = e.target.value;
        setCategory(selectedCategory);

        if (selectedCategory) {
            try {
                const response = await axios.get(`http://localhost/grocery-store/backend/api/getProductsByCategory.php?category=${selectedCategory}`);
                if (Array.isArray(response.data)) {
                    setProducts(response.data);
                    setError('');
                } else if (response.data.error) {
                    setProducts([]);
                    setError(response.data.error);
                }
            } catch (err) {
                console.error(err);
                setProducts([]);
                setError('Failed to fetch products.');
            }
        } else {
            setProducts([]);
        }
    };

    return (
        <div className="product-selector">
            <h2>Select Category</h2>
            <select value={category} onChange={handleCategoryChange}>
                <option value="">Select a category</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Meat">Meat</option>
            </select>

            {error && <p style={{ color: 'red' }}>{error}</p>}

            <div className="products">
                {Array.isArray(products) && products.map((product) => (
                    <div key={product.id} className="product">
                        <h3>{product.name}</h3>
                        <img
                            src={`/assets/${product.image}`}
                            alt={product.name}
                            style={{ width: '150px', height: '150px' }}
                        />
                        <p>Price: ${product.price}</p>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ProductSelector;
