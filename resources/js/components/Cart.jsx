import React, { Component, useEffect } from "react";
import { createRoot } from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";
import $ from "jquery";
import "bootstrap-select/dist/css/bootstrap-select.min.css";
import "bootstrap-select/dist/js/bootstrap-select.min.js";
import QRCodeScanner from "./QRCodeScanner";
import CameraComponent from "./CameraComponent";

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            barcode: "",
            search: "",
            customer_id: "",
            customer_proof: "",
            customer_notes: "",
            translations: {},
            showScanner: false,
            showCamera: false,
            capturedImages: [],
            selectedImages: [],
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleScanBarcodeForm = this.handleScanBarcodeForm.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);

        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.setCustomerProof = this.setCustomerProof.bind(this);
        this.setCustomerNotes = this.setCustomerNotes.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
        this.loadTranslations = this.loadTranslations.bind(this);

        this.handleScanComplete = this.handleScanComplete.bind(this);
        this.handleHideScanner = this.handleHideScanner.bind(this);

        this.handleOpenCamera = this.handleOpenCamera.bind(this);
        this.handleCaptureImage = this.handleCaptureImage.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);

        this.handleUploadImages = this.handleUploadImages.bind(this);
    }

    componentDidMount() {
        // load user cart
        this.loadCustomers();
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
    }

    handleOpenCamera = () => {
        this.setState({ showCamera: true });
    };

    handleCaptureImage = (dataURL) => {
        this.setState((prevState) => ({
            capturedImages: [...prevState.capturedImages, dataURL],
        }));
    };

    handleCloseModal = () => {
        this.setState({ showCamera: false });
    };

    handleUploadImages = async (event) => {
        const files = Array.from(event.target.files);
        const images = [];

        const readFile = (file) =>
            new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = (err) => reject(err);
                reader.readAsDataURL(file);
            });

        for (const file of files) {
            try {
                const image = await readFile(file);
                images.push(image);
            } catch (error) {
                console.log("Error reading file:", error);
            }
        }

        console.log(images);
        this.setState({ selectedImages: images });
    };

    // load the transaltions for the react component
    loadTranslations() {
        axios
            .get("/admin/locale/cart")
            .then((res) => {
                const translations = res.data;
                this.setState({ translations });
            })
            .catch((error) => {
                console.error("Error loading translations:", error);
            });
    }

    handleScanComplete(barcode) {
        console.log("Scanned value:", barcode);
        this.setState({ showScanner: false });
        this.setState({ barcode });
        this.handleScanBarcode(barcode);
    }

    loadCustomers() {
        axios.get(`/admin/customers`).then((res) => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `?search=${search}&active=1` : "?active=1";
        axios.get(`/admin/products${query}`).then((res) => {
            const products = res.data.data;
            this.setState({ products });
        });
    }

    handleOnChangeBarcode = (event) => {
        const barcode = event.target.value;
        this.setState({ barcode });
    };

    handleHideScanner = () => {
        this.setState({ showScanner: false });
    };

    loadCart() {
        axios.get("/admin/cart").then((res) => {
            const cart = res.data;
            this.setState({ cart });
        });
    }

    handleScanBarcodeForm = (event) => {
        event.preventDefault();
        const { barcode } = this.state;
        this.handleScanBarcode(barcode);
    };

    handleScanBarcode = (barcode) => {
        if (!!barcode) {
            axios
                .post("/admin/cart", { barcode })
                .then((res) => {
                    this.loadCart();
                    this.setState({ barcode: "" });
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    };
    handleChangeQty(product_id, qty, days) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
                c.pivot.days = days;
            }
            return c;
        });

        this.setState({ cart });
        if (!qty) return;

        axios
            .post("/admin/cart/change-qty", { product_id, quantity: qty, days })
            .then((res) => {})
            .catch((err) => {
                Swal.fire("Error!", err.response.data.message, "error");
                const updatedCart = this.state.cart.map((c) => {
                    if (c.id === product_id) {
                        c.pivot.quantity = c.quantity;
                    }
                    return c;
                });
                this.setState({ cart: updatedCart });
            });
    }

    getTotal(cart) {
        const total = cart.map(
            (c) => c.pivot.quantity * c.price * c.pivot.days
        );
        return sum(total).toFixed(2);
    }
    handleClickDelete(product_id) {
        axios
            .post("/admin/cart/delete", { product_id, _method: "DELETE" })
            .then((res) => {
                const cart = this.state.cart.filter((c) => c.id !== product_id);
                this.setState({ cart });
            });
    }
    handleEmptyCart() {
        axios.post("/admin/cart/empty", { _method: "DELETE" }).then((res) => {
            this.setState({ cart: [] });
        });
    }
    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }
    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(barcode) {
        let product = this.state.products.find((p) => p.barcode === barcode);
        if (!!product) {
            // if product is already in cart
            let cart = this.state.cart.find((c) => c.id === product.id);
            if (!!cart) {
                // update quantity
                this.setState({
                    cart: this.state.cart.map((c) => {
                        if (
                            c.id === product.id &&
                            product.quantity > c.pivot.quantity
                        ) {
                            c.pivot.quantity = c.pivot.quantity + 1;
                        }
                        return c;
                    }),
                });
            } else {
                if (product.quantity > 0) {
                    product = {
                        ...product,
                        pivot: {
                            days: 1,
                            quantity: 1,
                            product_id: product.id,
                            user_id: 1,
                            white_label_id: product.white_label_id,
                        },
                    };

                    this.setState({ cart: [...this.state.cart, product] });
                }
            }

            axios
                .post("/admin/cart", { barcode })
                .then((res) => {
                    // this.loadCart();
                    console.log(res);
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }
    setCustomerProof(event) {
        this.setState({ customer_proof: event.target.value });
    }
    setCustomerNotes(event) {
        this.setState({ customer_notes: event.target.value });
    }

    handleClickSubmit = () => {
        const fileInput = document.getElementById("upload");
        const additionalFiles = Array.from(fileInput.files);
    
        Swal.fire({
            title: this.state.translations["received_amount"],
            input: "text",
            inputValue: this.getTotal(this.state.cart),
            cancelButtonText: this.state.translations["cancel_pay"],
            showCancelButton: true,
            confirmButtonText: this.state.translations["confirm_pay"],
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                // Create form data
                const formData = new FormData();
                formData.append("customer_id", this.state.customer_id);
                formData.append("customer_proof", this.state.customer_proof);
                formData.append("customer_notes", this.state.customer_notes);
                formData.append("amount", amount);
                formData.append("capturedImages", this.state.capturedImages);

                if(this.state.selectedImages.length)
                    additionalFiles.forEach((file) => {
                        formData.append("additionalFiles[]", file);
                    });
    
                return axios
                    .post("/admin/orders", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((res) => {
                        this.loadCart();
                        //window.location.href = "/admin/orders";
                        return res.data;
                    })
                    .catch((err) => {
                        Swal.showValidationMessage(err.response.data.message);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                //
            }
        });
    };
    
    
    render() {
        $(".selectpicker").selectpicker("refresh");
        const {
            cart,
            products,
            customers,
            barcode,
            translations,
            showScanner,
            showCamera,
            selectedImages,
        } = this.state;
        return (
            <>
                <div className="row flex-row-reverse">
                    <div className="col mb-2">
                        <button
                            type="button"
                            className="btn btn-dark btn-block btn-sm"
                            onClick={this.handleOpenCamera.bind(this)}
                        >
                            <i className="fas fa-camera"></i> Capture
                        </button>
                    </div>
                    <div className="col">
                        <label
                            htmlFor="upload"
                            className="btn btn-primary btn-block btn-sm"
                        >
                            <i className="fas fa-upload"></i> Upload
                        </label>
                        <input
                            id="upload"
                            type="file"
                            multiple
                            style={{ display: "none" }}
                            onChange={this.handleUploadImages}
                            accept="image/*"
                        />
                    </div>
                </div>
                <div className="mb-2 images_captured">
                    {selectedImages.length > 0 && (
                        <div className="row mt-3">
                            <div className="col">
                                <div>
                                    <strong>Selected Images</strong>
                                </div>
                                <div className="image-preview">
                                    {selectedImages.map((image, index) => (
                                        <img
                                            key={index}
                                            src={image}
                                            alt={`Selected Image ${index}`}
                                            className="preview-image"
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}
                    {this.state.capturedImages.length != 0 && (
                        <div>
                            <strong>Captured Images</strong>
                        </div>
                    )}
                    {this.state.capturedImages.map((image, index) => (
                        <img
                            key={index}
                            src={image}
                            alt={`Captured Image ${index}`}
                        />
                    ))}
                </div>
                <div className="row">
                    {showCamera && (
                        <div
                            className="modal"
                            tabIndex="-1"
                            role="dialog"
                            style={{ display: "block" }}
                        >
                            <div
                                className="modal-dialog modal-fullscreen"
                                role="document"
                            >
                                <div className="modal-content">
                                    <div className="modal-header">
                                        <h5
                                            className="modal-title"
                                            id="exampleModalLabel"
                                        >
                                            Capture Image
                                        </h5>
                                        <button
                                            type="button"
                                            className="close"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                            onClick={this.handleCloseModal}
                                        >
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div className="position-relative">
                                        <CameraComponent
                                            onCapture={this.handleCaptureImage}
                                        />
                                    </div>
                                    <div className="images_captured">
                                        {this.state.capturedImages.map(
                                            (image, index) => (
                                                <img
                                                    key={index}
                                                    src={image}
                                                    alt={`Captured Image ${index}`}
                                                />
                                            )
                                        )}
                                    </div>
                                    <div className="modal-footer">
                                        <button
                                            type="button"
                                            className="btn btn-secondary btn-sm"
                                            onClick={this.handleCloseModal}
                                        >
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                    {showScanner && (
                        <div
                            className="modal"
                            tabIndex="-1"
                            role="dialog"
                            style={{ display: "block" }}
                        >
                            <div className="modal-dialog" role="document">
                                <div className="modal-content">
                                    <div className="modal-header">
                                        <h5
                                            className="modal-title"
                                            id="exampleModalLabel"
                                        >
                                            Scan Product QR Code
                                        </h5>
                                        <button
                                            type="button"
                                            className="close"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                            onClick={this.handleHideScanner}
                                        >
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <QRCodeScanner
                                        onScanComplete={this.handleScanComplete}
                                    />

                                    <div className="modal-footer">
                                        <button
                                            type="button"
                                            className="btn btn-secondary btn-sm"
                                            onClick={this.handleHideScanner}
                                        >
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                    <div className="col-md-6 col-lg-6 order-md-0 order-sm-1">
                        <div className="row mb-2">
                            <div className="col" style={{ maxWidth: "70px" }}>
                                {!showScanner && (
                                    <button
                                        className="btn btn-dark"
                                        onClick={() =>
                                            this.setState({ showScanner: true })
                                        }
                                    >
                                        <i className="fas fa-qrcode"></i>
                                    </button>
                                )}
                            </div>
                            <div className="col">
                                <form onSubmit={this.handleScanBarcodeForm}>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="QR Code"
                                        value={barcode}
                                        onChange={this.handleOnChangeBarcode}
                                    />
                                </form>
                            </div>
                            <div className="col">
                                {customers.length > 0 ? (
                                    <select
                                        className="form-control selectpicker"
                                        onChange={this.setCustomerId}
                                        data-live-search="true"
                                        title="Select Customer"
                                        data-size="5" // Set the number of visible options
                                    >
                                        {customers.map((cus) => (
                                            <option key={cus.id} value={cus.id}>
                                                {`${cus.first_name} ${cus.last_name}`}
                                            </option>
                                        ))}
                                    </select>
                                ) : (
                                    <p>Loading customers...</p>
                                )}
                            </div>
                        </div>
                        <div className="user-cart">
                            <div className="card">
                                <table className="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                {translations["product_name"]}
                                            </th>
                                            <th>{translations["quantity"]}</th>
                                            <th>Days</th>
                                            <th className="text-right">
                                                {translations["price"]}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cart.map((c) => (
                                            <tr key={c.id}>
                                                <td>{c.name}</td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-sm qty"
                                                        value={c.pivot.quantity}
                                                        onChange={(event) =>
                                                            this.handleChangeQty(
                                                                c.id,
                                                                event.target
                                                                    .value,
                                                                c.pivot.days
                                                            )
                                                        }
                                                    />
                                                    <button
                                                        className="btn btn-danger btn-sm"
                                                        onClick={() =>
                                                            this.handleClickDelete(
                                                                c.id
                                                            )
                                                        }
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        className="form-control form-control-sm days"
                                                        value={c.pivot.days}
                                                        onChange={(event) =>
                                                            this.handleChangeQty(
                                                                c.id,
                                                                c.pivot
                                                                    .quantity,
                                                                event.target
                                                                    .value
                                                            )
                                                        }
                                                    />
                                                </td>
                                                <td className="text-right">
                                                    {window.APP.currency_symbol}{" "}
                                                    {(
                                                        c.price *
                                                        c.pivot.quantity
                                                    ).toFixed(2)}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col">{translations["total"]}:</div>
                            <div className="col text-right">
                                {window.APP.currency_symbol}{" "}
                                {this.getTotal(cart)}
                            </div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <select
                                    className="form-control mb-2 selectpicker"
                                    value={this.state.customer_proof}
                                    onChange={this.setCustomerProof}
                                    data-live-search="true"
                                >
                                    <option value="">Select Proof</option>
                                    <option value="Aadhaar Card">Aadhaar Card</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Voter ID Card">Voter ID Card</option>
                                    <option value="PAN Card">PAN Card</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Identity Card">Identity Card</option>
                                    <option value="Ration Card">Ration Card</option>
                                    <option value="Bank Passbook">Bank Passbook</option>
                                    <option value="Birth Certificate">Birth Certificate</option>
                                    <option value="Caste Certificate">Caste Certificate</option>
                                    <option value="Income Certificate">Income Certificate</option>
                                    <option value="Residence Certificate">Residence Certificate</option>
                                    <option value="Employment Card">Employment Card</option>
                                    <option value="Student ID Card">Student ID Card</option>
                                    <option value="Health Card">Health Card</option>
                                    <option value="Senior Citizen Card">Senior Citizen Card</option>
                                    <option value="Army ID Card">Army ID Card</option>
                                    <option value="Ration Smart Card">Ration Smart Card</option>
                                </select>
                                <textarea
                                    placeholder="Details/Notes"
                                    className="form-control mb-2"
                                    onChange={this.setCustomerNotes}
                                    value={this.state.CustomerNotes}
                                ></textarea>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-danger btn-block"
                                    onClick={this.handleEmptyCart}
                                    disabled={
                                        !cart.length || !this.state.customer_id
                                    }
                                >
                                    {translations["cancel"]}
                                </button>
                            </div>
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-primary btn-block"
                                    disabled={
                                        !cart.length || !this.state.customer_id
                                    }
                                    onClick={this.handleClickSubmit}
                                >
                                    {translations["checkout"]}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-6 col-lg-6">
                        <div className="mb-2">
                            <input
                                type="text"
                                className="form-control"
                                placeholder={
                                    translations["search_product"] + "..."
                                }
                                onChange={this.handleChangeSearch}
                                onKeyDown={this.handleSeach}
                            />
                        </div>
                        <div className="order-product">
                            {products.map((p) => (
                                <div
                                    onClick={() =>
                                        this.addProductToCart(p.barcode)
                                    }
                                    key={p.id}
                                    className="item"
                                >
                                    <img
                                        src={
                                            p.image_url == "/storage/"
                                                ? "/images/default.png"
                                                : p.image_url
                                        }
                                        alt=""
                                    />
                                    <h5
                                        style={
                                            window.APP.warning_quantity >
                                            p.quantity
                                                ? { color: "red" }
                                                : {}
                                        }
                                    >
                                        {p.name}({p.quantity})
                                    </h5>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </>
        );
    }
}

export default Cart;

const root = document.getElementById("cart");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Cart />);
}
