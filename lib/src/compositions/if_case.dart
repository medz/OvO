import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

/// A class that implements the OvO interface and provides an if-else-like composition.
///
/// The `If` class takes a condition, a `then` branch, and an optional `orElse` branch.
/// If the condition is true, the `then` branch is executed, otherwise the `orElse` branch is executed.
/// If there is no `orElse` branch and the condition is false, an `OvOException` is thrown.
///
/// Example usage:
/// ```dart
/// final schema = If(
///   condition: SomeConditionSchema(),
///   then: SomeSchema(),
///   orElse: SomeOtherSchema(),
/// );
/// ```
class If<T> implements OvO<T> {
  /// Represents the condition for the if case.
  final OvO condition;

  /// The `then` property is an instance of `OvO` class.
  /// It represents the value to be returned if the condition in the `if` statement is true.
  final OvO<T> then;

  /// An optional value of type [OvO] that is used as a fallback when the main value is null.
  final OvO<T>? orElse;

  /// Error message to be thrown when the condition is false and there is no `orElse` branch.
  final String? message;

  /// Creates a If-case [OvO] schema.
  ///
  /// - [condition] is the condition for the if case.
  /// - [then] is the value to be returned if the condition validate passes.
  /// - [orElse] is the value to be returned if the condition is false.
  ///
  /// If [orElse] is not provided, an `OvOException` is thrown when the
  /// condition is false.
  const If(this.condition, {required this.then, this.orElse, this.message});

  @override
  Future<T> handle(Context context, data) async {
    return switch (await _tryCondition(context, data)) {
      true => await _tryThen(context, data),
      false => await _tryOrElse(context, data),
    };
  }

  Future<T> _tryOrElse(Context context, data) async {
    if (orElse == null) {
      throw OvOException(code: #if_case, message: message ?? 'No else case');
    }

    return orElse!.handle(context, data);
  }

  Future<T> _tryThen(Context context, data) {
    return then.handle(context, data);
  }

  Future<bool> _tryCondition(Context context, data) async {
    try {
      await condition.handle(context, data);

      return true;
    } catch (_) {
      return false;
    }
  }
}
