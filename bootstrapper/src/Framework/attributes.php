

#[Attribute]
class WPAction {
    function __construct(public string $actionName, public int $priority = 10) {}
}
